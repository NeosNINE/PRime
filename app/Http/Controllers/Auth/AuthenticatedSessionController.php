<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('index');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function store(LoginRequest $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        try {
            $request->authenticate();

            $request->session()->regenerate();

            // Если у пользователя включена 2FA — требуем код. Не завершаем логин до верификации
            $user = Auth::user();
            if ($user && $user->two_factor_enabled) {
                Auth::logout();
                // Сохраняем временный идентификатор пользователя в сессии для шага 2FA
                session(['login_2fa_user_id' => $user->id]);

                if ($request->ajax() || $request->boolean('ajax')) {
                    return response()->json([
                        'success' => false,
                        'require_2fa' => true,
                        'message' => 'Требуется код двухфакторной аутентификации',
                        'csrf' => csrf_token(),
                    ], 202);
                }

                return redirect()->route('login');
            }

            // Фиксируем успешный вход: ip, время, уведомление о входе
            if ($user = Auth::user()) {
                try {
                    $ip = $request->ip();
                    $user->last_login_ip = $ip;
                    $user->last_login_at = now();
                    $user->save();

                    [$city, $country] = geo()->getCityCountry($ip);
                    $location = trim(($city ? $city : '') . ($country ? ( $city ? ', ' : '' ) . $country : ''));
                    $title = 'Вход с нового IP-адреса';
                    $text = 'Зафиксирован вход в ваш аккаунт: IP ' . $ip . ($location ? ' (' . $location . ')' : '') . '. Если это были не вы — смените пароль и включите 2FA.';
                    notifyUser($user, $title, $text, null, 'fas fa-shield-alt', 'security');
                } catch (\Throwable $e) {
                    // игнорируем, не блокируем логин
                }
            }

            // Return JSON response for AJAX requests (header or ajax param)
            if ($request->ajax() || $request->boolean('ajax')) {
                return response()->json([
                    'success' => true,
                    'redirect' => RouteServiceProvider::getRedirectUrl()
                ]);
            }

            return redirect()->intended(RouteServiceProvider::getRedirectUrl());
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors for AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }

            throw $e;
        }
    }

    // Верификация 2FA кода и завершение логина
    public function verifyTwoFactor(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string']
        ]);

        $userId = session('login_2fa_user_id');
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Сессия 2FA не найдена'], 422);
        }

        $user = \App\Models\User::find($userId);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Пользователь не найден'], 422);
        }

        if (!users()->verifyTwoFactor($user, $request->string('code'))) {
            return response()->json(['success' => false, 'errors' => ['code' => ['Неверный код']]], 422);
        }

        // Авторизуем и очищаем временную метку
        Auth::login($user, true);
        session()->forget('login_2fa_user_id');
        session()->regenerate();

        // Фиксируем вход и уведомление после успешной 2FA
        try {
            $ip = $request->ip();
            $user->last_login_ip = $ip;
            $user->last_login_at = now();
            $user->save();

            [$city, $country] = geo()->getCityCountry($ip);
            $location = trim(($city ? $city : '') . ($country ? ( $city ? ', ' : '' ) . $country : ''));
            $title = 'Вход с нового IP-адреса';
            $text = 'Зафиксирован вход в ваш аккаунт: IP ' . $ip . ($location ? ' (' . $location . ')' : '') . '. Если это были не вы — смените пароль и включите 2FA.';
            notifyUser($user, $title, $text, null, 'fas fa-shield-alt', 'security');
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'redirect' => RouteServiceProvider::getRedirectUrl()
        ]);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|String
     */
    public function destroy(Request $request): string|RedirectResponse|Redirector
    {

        //Если logout делает admin авторизованный под юзером
        if (users()->isImpersonating()) {

            //Выходим из под авторизации юзера
            users()->leaveImpersonation();

            //Закрываем вкладку
            return '<script>window.close()</script>';


            //Если logout делает обычный юзер
        } else {


            Auth::guard('web')->logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            return redirect('/');
        }
    }
}
