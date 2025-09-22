<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Services\UsersService;

class UserProfileController extends Controller
{

    public function __construct(){

        $this->middleware('auth');

    }


    public function __invoke(): View
    {

        $user = Auth::user();

        return view('user.app.pages.profile',[
            'user' => $user
        ]);

    }


    public function updateEmail(Request $request, UsersService $usersService): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string']
        ]);

        $user = Auth::user();

        $usersService->updateEmailForUser($user, $request->string('email'), $request->string('password'));

        return response()->json([
            'success' => true,
            'message' => 'Email успешно изменен',
            'email' => $user->email
        ]);
    }


    public function updateAvatar(Request $request, UsersService $usersService): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'file']
        ]);

        $user = Auth::user();

        $publicUrl = $usersService->updateAvatarForUser($user, $request->file('avatar'));

        return response()->json([
            'success' => true,
            'message' => 'Аватар обновлён',
            'avatar_url' => $publicUrl
        ]);
    }


    public function updatePassword(Request $request, UsersService $usersService): JsonResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8']
        ]);

        $user = Auth::user();

        $usersService->updatePasswordForUser(
            $user,
            $request->string('current_password'),
            $request->string('new_password')
        );

        return response()->json([
            'success' => true,
            'message' => 'Пароль успешно изменён'
        ]);
    }


    // ===== 2FA API =====
    public function twoFactorStatus(Request $request): JsonResponse
    {
        $user = Auth::user();
        return response()->json([
            'enabled' => (bool)$user->two_factor_enabled,
            'has_secret' => (bool)$user->two_factor_secret,
        ]);
    }

    public function twoFactorSetup(Request $request, UsersService $usersService): JsonResponse
    {
        $user = Auth::user();
        $data = $usersService->generateTwoFactorSecret($user);
        return response()->json([
            'success' => true,
            'secret' => $data['secret'],
            'otpauth' => $data['otpauth']
        ]);
    }

    public function twoFactorEnable(Request $request, UsersService $usersService): JsonResponse
    {
        $request->validate([
            'secret' => ['required', 'string'],
            'code' => ['required', 'string']
        ]);

        $user = Auth::user();
        $usersService->enableTwoFactor($user, $request->string('secret'), $request->string('code'));

        return response()->json([
            'success' => true,
            'message' => 'Двухфакторная аутентификация включена',
            'recovery_codes' => json_decode($user->two_factor_recovery_codes, true)
        ]);
    }

    public function twoFactorDisable(Request $request, UsersService $usersService): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string']
        ]);

        $user = Auth::user();
        $usersService->disableTwoFactor($user, $request->string('code'));

        return response()->json([
            'success' => true,
            'message' => 'Двухфакторная аутентификация отключена'
        ]);
    }

}
