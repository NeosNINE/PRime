<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Services\UsersService;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('index');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store( Request $request, UsersService $usersService )
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Пытаемся найти пользователя
        $email = (string)$request->input('email');
        $user = User::where('email', $email)->first();

        // Всегда возвращаем успешный ответ для безопасности (не раскрывать наличие e-mail)
        if (!$user) {
            if ($request->ajax() || $request->boolean('ajax')) {
                return response()->json(['success' => true, 'status' => __(
                    Password::RESET_LINK_SENT
                )]);
            }
            return back()->with('status', __(Password::RESET_LINK_SENT));
        }

        // Генерируем токен и отправляем письмо через наш Emails сервис, чтобы было видно в админке
        $token = Password::broker()->createToken($user);

        $usersService->sendPasswordResetEmail($user, $token);

        if ($request->ajax() || $request->boolean('ajax')) {
            return response()->json(['success' => true, 'status' => __(Password::RESET_LINK_SENT)]);
        }

        return back()->with('status', __(Password::RESET_LINK_SENT));
    }
}
