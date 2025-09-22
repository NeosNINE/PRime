<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function redirect(Request $request)
    {
        $clientId = Config::get('services.google.client_id');
        $redirectUri = Config::get('services.google.redirect');
        $scope = urlencode('openid email profile');
        $state = Str::random(32);
        $intent = $request->query('intent');

        session(['google_oauth_state' => $state, 'google_oauth_intent' => $intent]);

        $url = 'https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'online',
            'include_granted_scopes' => 'true',
            'state' => $state,
            'prompt' => 'select_account',
        ]);

        return redirect()->away($url);
    }

    public function callback(Request $request)
    {
        $state = $request->query('state');
        if (!$state || $state !== session('google_oauth_state')) {
            return redirect()->route('login')->withErrors(['email' => 'Неверное состояние OAuth. Попробуйте снова.']);
        }

        $code = $request->query('code');
        if (!$code) {
            return redirect()->route('login')->withErrors(['email' => 'Не удалось получить код авторизации Google.']);
        }

        $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code' => $code,
            'client_id' => Config::get('services.google.client_id'),
            'client_secret' => Config::get('services.google.client_secret'),
            'redirect_uri' => Config::get('services.google.redirect'),
            'grant_type' => 'authorization_code',
        ]);

        if (!$tokenResponse->ok()) {
            return redirect()->route('login')->withErrors(['email' => 'Не удалось обменять код на токен Google.']);
        }

        $accessToken = $tokenResponse->json('access_token');
        $idToken = $tokenResponse->json('id_token');

        // Получаем профиль
        $userInfo = Http::withToken($accessToken)->get('https://www.googleapis.com/oauth2/v2/userinfo');
        if (!$userInfo->ok()) {
            return redirect()->route('login')->withErrors(['email' => 'Не удалось получить данные профиля Google.']);
        }

        $googleId = $userInfo->json('id');
        $email = $userInfo->json('email');
        $name = $userInfo->json('name');
        $avatar = $userInfo->json('picture');

        if (!$googleId) {
            return redirect()->route('login')->withErrors(['email' => 'Профиль Google без идентификатора.']);
        }

        // Ищем пользователя
        $user = User::where('google_id', $googleId)
            ->orWhere(function($q) use ($email) {
                if ($email) $q->where('email', $email);
            })
            ->first();

        if (!$user) {
            // Регистрация нового пользователя (email может быть null, допускаем гостевой вход)
            $user = new User();
            $user->email = $email;
            $user->google_id = $googleId;
            if (empty($user->password)) {
                $user->password = bcrypt(Str::random(32));
            }
            // Установим логин: имя из профиля Google, либо часть email до @, либо "user_" + 6 символов
            $baseLogin = $name ?: ($email ? explode('@', $email)[0] : null);
            $login = $this->makeUniqueLogin($baseLogin);
            $user->login = $login;
        } else {
            // Привязываем google_id, если отсутствует
            if (!$user->google_id) {
                $user->google_id = $googleId;
            }
            // Если у пользователя пустой логин — заполним из Google
            if (empty($user->login)) {
                $baseLogin = $name ?: ($email ? explode('@', $email)[0] : null);
                $user->login = $this->makeUniqueLogin($baseLogin);
            }
        }

        // Обновляем аватар, если пришёл
        if ($avatar) {
            $user->google_avatar = $avatar;
            if (empty($user->avatar)) {
                $user->avatar = $avatar; // показываем гугл-аватар, если своего нет
            }
        }

        // Сохраняем
        $user->save();

        // Если включена 2FA — не авторизуем сразу, просим код
        if ($user->two_factor_enabled) {
            session(['login_2fa_user_id' => $user->id]);
            // Отправим на главную с параметром, чтобы фронт открыл модалку 2FA
            return redirect('/?two_factor=1');
        }

        // Иначе авторизуем
        Auth::login($user, true);

        // Зафиксируем вход и уведомим
        try {
            $ip = request()->ip();
            $user->last_login_ip = $ip;
            $user->last_login_at = now();
            $user->save();

            [$city, $country] = geo()->getCityCountry($ip);
            $location = trim(($city ? $city : '') . ($country ? ( $city ? ', ' : '' ) . $country : ''));
            $title = 'Вход с нового IP-адреса';
            $text = 'Зафиксирован вход в ваш аккаунт: IP ' . $ip . ($location ? ' (' . $location . ')' : '') . '. Если это были не вы — смените пароль и включите 2FA.';
            notifyUser($user, $title, $text, null, 'fas fa-shield-alt', 'security');
        } catch (\Throwable $e) {}

        $redirect = \App\Providers\RouteServiceProvider::getRedirectUrl();
        return redirect($redirect);
    }

    private function makeUniqueLogin(?string $base): string
    {
        $candidate = trim((string)$base);
        if ($candidate === '') {
            $candidate = 'user_'.Str::lower(Str::random(6));
        }

        // Разрешённые символы: буквы, цифры, точка, тире, подчёркивание
        $candidate = preg_replace('/[^a-zA-Z0-9._-]/', '_', $candidate);

        // Ограничим длину 30
        $candidate = substr($candidate, 0, 30);

        // Проверка уникальности
        $original = $candidate;
        $i = 1;
        while (User::where('login', $candidate)->exists()) {
            $suffix = '_'.$i;
            $candidate = substr($original, 0, 30 - strlen($suffix)).$suffix;
            $i++;
            if ($i > 1000) { // аварийный выход
                $candidate = 'user_'.Str::lower(Str::random(8));
                break;
            }
        }

        return $candidate;
    }
}


