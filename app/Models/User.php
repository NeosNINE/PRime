<?php

namespace App\Models;

use App\Models\System\Role;
use App\Models\Traits\ModelTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, ModelTrait;

    protected $fillable = [
        'login',
        'email',
        'password',
        'google_id',
        'google_avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_active_at' => 'datetime',
    ];



    /**
     * Роли пользователя
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }


    /**
     * Является ли юзер суперадмином
     */
    public function isSuperAdmin(): bool
    {

        return roles()->isSuperAdmin($this);

    }


    /**
     * Является ли юзер другим суперадмином (по сравнению с авторизованным пользователем)
     */
    public function isAnotherSuperAdmin(): bool
    {

        if( !$this->isSuperAdmin() )
            return false;

        if( !auth()->check() || $this->id != auth()->id() )
            return true;

        return false;

    }


    /**
     * Является ли юзер админом (если юзер является Super Admin, этот метод так же вернет TRUE)
     */
    public function isAdmin(): bool
    {

        return roles()->isAdmin($this);

    }


    /**
     * Является ли юзер обычным админом (если является Super Admin, но не является обычным админом - вернет FALSE)
     * Если юзер Super Admin - вернет FALSE
     */
    public function isUsualAdmin(): bool
    {

        return roles()->isUsualAdmin($this);

    }


    /**
     * Проверяем имеет ли пользователь хоть какие-то доступы к админке
     */
    public function isHasAnyAdminAccess(): bool
    {

        return roles()->isHasAnyAdminAccess($this);

    }


    /**
     * Является ли юзер разработчиком
     */
    public function isDeveloper(): bool
    {

        return roles()->isDeveloper($this);

    }



    /**
     * Avatar
     */
    public function getAvatarAttribute( $avatar ): string
    {

        // Приоритет: пользовательский аватар; если его нет — google_avatar; иначе дефолт
        if( $avatar )
            return $avatar;

        if( $this->google_avatar )
            return $this->google_avatar;

        return asset('assets/user/img/default-avatar.svg');

    }


    /**
     * Есть ли у пользователя кастомный аватар (а не дефолтный)
     */
    public function getHasAvatarAttribute(): bool
    {
        // Считаем, что «есть аватар», если есть кастомный avatar или google_avatar
        if( $this->getRawOriginal('avatar') )
            return true;

        return (bool) $this->getAttribute('google_avatar');
    }


    /**
     * Инициалы для плейсхолдера аватара
     */
    public function getAvatarInitialsAttribute(): string
    {
        $base = $this->login ?? ($this->email ? explode('@', $this->email)[0] : 'L');

        if (function_exists('mb_substr') && function_exists('mb_strtoupper')) {
            return mb_strtoupper(mb_substr($base, 0, 1));
        }

        return strtoupper(substr($base, 0, 1));
    }


    /**
     * Отправка Email о регистрации или подтверждения
     * @throws \Exception
     */
    public function sendEmailVerificationNotification(): bool
    {

        if( !$this->email )
            return false;

        if( config('settings.users_must_verify_email') )
            return users()->sendVerifyEmail($this);

        return users()->sendRegistrationEmail($this);

    }


    /**
     * Отправка Email с восстановлением пароля
     * @throws \Exception
     */
    public function sendPasswordResetNotification($token): bool
    {

        if( !$this->email )
            return false;

        return users()->sendPasswordResetEmail($this, $token);
    }

}
