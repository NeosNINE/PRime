<?php

namespace App\Services;

use App\Extra\Services\Traits\ServiceTrait;
use App\Models\User;
use App\Extra\Services\Service;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use OTPHP\TOTP;
use ParagonIE\ConstantTime\Base32;

class UsersService extends Service
{

    use ServiceTrait;


    /**
     * Исключаемые поля из поиска (при вывозе $this->get() с параметром search)
     */
    protected function exceptFieldsFromSearch(): array
    {
        return [
            'remember_token',
            'password',
            'email_verified_at'
        ];
    }


    /**
     * Подготовить данные для добавления
     */
    protected function addDataPrepare( array $data ): array
    {

        $data['password'] = Hash::make($data['password']);

        return $data;

    }



    /**
     * Запускается после добавления пользователя в БД
     */
    protected function afterAdd( array $data, User $user ): void
    {
        event(new Registered($user));
    }



    /**
     * Подготовить данные для редактирования
     */
    protected function editDataPrepare( array $data ): array
    {

        if( isset($data['new_password']) )
            $data['password'] = Hash::make($data['new_password']);

        return $data;

    }


    /**
     * Перед редактированием
     */
    protected function beforeEdit( array $data, User $user ): void
    {

        if( $user->isAnotherSuperAdmin() )
            error('Вы не можете редактировать другого Super Admin.');

    }


    /**
     * Перед удалением
     */
    protected function beforeDelete( User $user ): void
    {

        if( roles()->isSuperAdmin($user) )
            abort(403, 'Super Admin не может быть удален.');


        if( roles()->isUsualAdmin() && roles()->isAdmin($user) )
            abort(403, 'У Вас нет доступа удалять другого администратора.');

    }


    /**
     * Можем ли мы установить связь
     */
    protected function canSetRelations( User $user, $relation_model, $relation ): bool
    {

        if( $relation['to'] == 'System\Role' )
            return roles()->canEditRoles($user, $relation_model);

        return true;

    }




    /**
     * Валидация при добавлении
     */
    public function addValidate( array $data ): void
    {

        Validator::make( $data, [
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ])->validate();

    }


    /**
     * Валидация при редактировании
     */
    public function editValidate( array $data, User $user ): void
    {

        Validator::make( $data, [
            'email' => [
                'email',
                'required',
                Rule::unique('users')->ignore($user->id)
            ]
        ])->validate();


        if( $data['email'] != $user->email && $user->id != Auth::id() ){

            if( roles()->isSuperAdmin($user) )
                abort(403, 'Вы не можете поменять email другому Super Admin.');

            if( roles()->isAdmin($user) && roles()->isUsualAdmin() )
                abort(403, 'У Вас нет доступа менять email другому администратору.');

        }


        if( isset($data['new_password']) && $user->id != Auth::id() ){

            if( roles()->isSuperAdmin($user) )
                abort(403, 'Вы не можете поменять пароль другому Super Admin.');

            if( roles()->isAdmin($user) && roles()->isUsualAdmin() )
                abort(403, 'У Вас нет доступа менять пароль другому администратору.');

        }

    }


    /**
     * Обновить email пользователя с проверкой текущего пароля
     * @throws ValidationException
     */
    public function updateEmailForUser(User $user, string $newEmail, string $currentPassword): void
    {
        // Проверка пароля
        if (!Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Неверный текущий пароль']
            ]);
        }

        // Совпадение
        if ($newEmail === $user->email) {
            throw ValidationException::withMessages([
                'email' => ['Новый email должен отличаться от текущего']
            ]);
        }

        // Уникальность email
        Validator::make(['email' => $newEmail], [
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)]
        ])->validate();

        $user->email = $newEmail;
        $user->save();

        // Создаём уведомление пользователю
        try {
            helperClass()->notifyUser(
                $user,
                'Email успешно изменен',
                'Ваш email адрес был успешно изменен на новый.',
                null,
                'fas fa-envelope',
                'account'
            );
        } catch (\Throwable $e) {}
    }

    /**
     * Обновить аватар пользователя. Возвращает публичный URL
     * @return string avatar public URL
     */
    public function updateAvatarForUser(User $user, UploadedFile $file): string
    {
        // Валидация файла
        Validator::make(['avatar' => $file], [
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120']
        ])->validate();

        // Сохраняем новый файл
        $path = $file->store('avatars', 'public');
        $publicUrl = Storage::disk('public')->url($path);

        // Удаляем старый, если он был и это наш storage
        $rawAvatar = $user->getAttributes()['avatar'] ?? null;
        if ($rawAvatar) {
            $oldPath = parse_url($rawAvatar, PHP_URL_PATH) ?: '';
            // Приводим /storage/avatars/... → avatars/...
            if (str_starts_with($oldPath, '/storage/')) {
                $oldRel = substr($oldPath, strlen('/storage/'));
                try { Storage::disk('public')->delete($oldRel); } catch (\Throwable $e) {}
            }
        }

        // Сохраняем URL (учитывая существующую логику доступа к аватару)
        $user->avatar = $publicUrl;
        $user->save();

        return $publicUrl;
    }

    /**
     * Сменить пароль пользователя
     * @throws ValidationException
     */
    public function updatePasswordForUser(User $user, string $currentPassword, string $newPassword): void
    {
        // Проверка текущего пароля
        if (!Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Неверный текущий пароль']
            ]);
        }

        // Валидация нового пароля (минимум 8 символов)
        Validator::make(['password' => $newPassword], [
            'password' => ['required', 'string', 'min:8']
        ])->validate();

        // Новый пароль не должен совпадать с текущим
        if (Hash::check($newPassword, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Новый пароль должен отличаться от текущего']
            ]);
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        // Уведомление о смене пароля
        try {
            $ip = request()->ip();
            $text = 'Ваш пароль был успешно изменен' . ($ip ? ' с IP ' . $ip : '');
            helperClass()->notifyUser(
                $user,
                'Пароль изменен',
                $text,
                null,
                'fas fa-lock',
                'security'
            );
        } catch (\Throwable $e) {}
    }



    /**
     * 2FA: сгенерировать секрет и otpauth URL
     */
    public function generateTwoFactorSecret(User $user): array
    {
        $secret = Base32::encodeUpper(random_bytes(20));
        $issuer = env('APP_NAME', 'SOCNET');
        $label = ($user->email ?: ('user_'.$user->id));

        $totp = TOTP::create($secret, 30, 'sha1', 6);
        $totp->setLabel($label);
        $totp->setIssuer($issuer);

        return [
            'secret' => $secret,
            'otpauth' => $totp->getProvisioningUri(),
        ];
    }

    /**
     * 2FA: включить (с верификацией кода)
     */
    public function enableTwoFactor(User $user, string $secret, string $code): void
    {
        $secret = strtoupper(trim($secret));
        $code = preg_replace('/\D+/', '', (string)$code);
        $totp = TOTP::create($secret, 30, 'sha1', 6);
        if (!$totp->verify($code, null, 2)) {
            throw ValidationException::withMessages(['code' => ['Неверный код подтверждения']]);
        }

        $user->two_factor_enabled = true;
        $user->two_factor_secret = $secret;
        $user->two_factor_recovery_codes = json_encode($this->generateRecoveryCodes());
        $user->save();
    }

    /**
     * 2FA: отключить (с проверкой кода)
     */
    public function disableTwoFactor(User $user, string $code): void
    {
        if (!$user->two_factor_enabled || !$user->two_factor_secret) {
            return;
        }

        $code = preg_replace('/\D+/', '', (string)$code);
        $secret = strtoupper(trim((string)$user->two_factor_secret));
        $totp = TOTP::create($secret, 30, 'sha1', 6);
        if (!$totp->verify($code, null, 2)) {
            throw ValidationException::withMessages(['code' => ['Неверный код']]);
        }

        $user->two_factor_enabled = false;
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->save();
    }

    /**
     * 2FA: проверка кода во время логина
     */
    public function verifyTwoFactor(User $user, string $code): bool
    {
        if (!$user->two_factor_enabled || !$user->two_factor_secret) {
            return true;
        }

        $code = preg_replace('/\D+/', '', (string)$code);
        $secret = strtoupper(trim((string)$user->two_factor_secret));
        $totp = TOTP::create($secret, 30, 'sha1', 6);
        return $totp->verify($code, null, 2);
    }

    /**
     * Сгенерировать набор резервных кодов
     */
    private function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(substr(bin2hex(random_bytes(5)), 0, 10));
        }
        return $codes;
    }

    /**
     * Получить авторизованного или нужного юзера
     * Если без параметра - получаем текущего (авторизованного) авторизованного
     * Если с параметром, то
     *  - если параметр число - выбираем пользователя по ID
     *  - если параметр, итак, объект юзера - то просто вернется этот объект
     */
    public function getUser( $user = false, $current_by_default = true, bool $with_abort = true ): mixed
    {

        $return = null;

        if ($user) {


            if (is_numeric($user)) {

                $return = ($user = User::find($user)) ? $user : false;

            } else {

                $return = (isset($user->id)) ? $user : false;

            }


        } elseif ($current_by_default) {

            $return = (Auth::check()) ? Auth::user() : false;

        }

        if (!$return && $with_abort)
            abort(500, 'You need to login.');

        return $return;

    }



    /**
     * Получить ключ для авторизации за юзера
     */
    public function getImpersonateKey( User $user ): string
    {
        return md5($user->id.$user->created_at.config('env.app_key'));
    }




    /**
     * Авторизоваться за юзера
     */
    public function impersonate( User $user, string $key = null ): bool
    {

        if( $user->isAnotherSuperAdmin() )
            abort(403, 'Вы не можете авторизоваться за другого Super Admin.');

        $md5_key = $this->getImpersonateKey($user);

        if( $key ){

            if( $md5_key != $key )
                abort(403, 'У Вас нет доступа');

        }else{

            roles()->checkAccessWithAbort('users.impersonate');

        }

        if( $this->isImpersonating() ){

            $impersonator_id = $this->getImpersonator()?->id ?? Auth::id();

        }else{

            $impersonator_id = Auth::id();

        }

        if( !Auth::loginUsingId( $user->id ) )
            return false;

        session()->put('impersonator_id', $impersonator_id);
        session()->save();

        return true;

    }


    /**
     * Вернуться назад за админа
     */
    public function leaveImpersonation(): bool
    {

        if( !$this->isImpersonating() )
            return false;

        $impersonator_id = session()->pull('impersonator_id');
        session()->forget('impersonator_id');
        session()->save();

        if( !$impersonator_id || Auth::id() == $impersonator_id )
            return false;


        if( Auth::loginUsingId( $impersonator_id ) )
            return true;

        return false;

    }


    /**
     * Проверка является ли сейчас админ авторизованный за юзера
     */
    public function isImpersonating(): bool
    {

       if( session('impersonator_id', false) )
           return true;

       return false;

    }


    /**
     * Получить админа, который был авторизован за юзера
     */
    public function getImpersonator(): ?User
    {

        return User::find( session('impersonator_id', 0) );

    }


    /**
     * Отправить сообщение об успешной регистрации (приходит либо сообщение о регистрации, либо о подтверждении почты)
     */
    public function sendRegistrationEmail( User $user ): bool
    {

        return (bool)emails()->sendMessage(
            $user->email,
            'Добро пожаловать в '.env('APP_NAME').'!',
            'Здравствуйте! Вы успешно прошли регистрацию.',
            route('user.profile'),
            'Войти в личный кабинет',
            'auth'
        );

    }


    /**
     * Отправить сообщение об подтверждении почты (приходит либо сообщение о регистрации, либо о подтверждении почты)
     */
    public function sendVerifyEmail( User $user ): bool
    {

        if( $user->hasVerifiedEmail() )
            return true;

        $url = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );

        return (bool)emails()->sendMessage(
            $user->email,
            'Подтвердите Вашу электронную почту в '.env('APP_NAME'),
            'Здравствуйте! Вы успешно прошли регистрацию.<br>Для подтверждения Вашей электронной почты перейдите по ссылке ниже.',
            $url,
            'Подтвердить электронную почту',
            'auth'
        );

    }


    /**
     * Отправить сообщение о смене пароля
     */
    public function sendPasswordResetEmail( User $user, $token ): bool
    {

        return (bool)emails()->sendMessage(
            $user->email,
            'Запрос на восстановление пароля в '.env('APP_NAME'),
            'Здравствуйте! Чтобы восстановить пароль нажмите на ссылку ниже. Если Вы не запрашивали восстановление пароля - просто проигнорируйте данное сообщение.',
            route('password.reset', [ 'token' => $token, 'email' => $user->email ]),
            'Восстановить пароль',
            'auth'
        );

    }

}
