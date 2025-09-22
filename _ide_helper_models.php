<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models\System{
/**
 * App\Models\System\Cfg
 *
 * @property int $id
 * @property string $key
 * @property string $value
 * @method static \Illuminate\Database\Eloquent\Builder|Cfg newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cfg newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cfg query()
 * @method static \Illuminate\Database\Eloquent\Builder|Cfg whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cfg whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cfg whereValue($value)
 */
	class Cfg extends \Eloquent {}
}

namespace App\Models\System{
/**
 * App\Models\System\ClientEvent
 *
 * @property int $id
 * @property string $event_name
 * @property array|null $data
 * @property string|null $access_key Какой доступ должна иметь роль для этого события
 * @property int $unique Если TRUE, то на клиенте только один раз будет выполняться за один AJAX
 * @property int|null $created_user_id User который запустил событие
 * @property int|null $for_user_id User которому предназначено событие
 * @property \Illuminate\Support\Carbon|null $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEvent whereAccessKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEvent whereCreatedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEvent whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEvent whereEventName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEvent whereForUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientEvent whereUnique($value)
 */
	class ClientEvent extends \Eloquent {}
}

namespace App\Models\System{
/**
 * App\Models\System\Email
 *
 * @property int $id
 * @property string|null $email На какой Email отправлять
 * @property int|null $user_id
 * @property string|null $subject Тема
 * @property string|null $text Сообщение (html)
 * @property string|null $text_plain Сообщение (text_plain)
 * @property string $type Тип
 * @property string $status Статус
 * @property string|null $key Ключ
 * @property \Illuminate\Support\Carbon|null $sent_date Время отправки
 * @property array|null $data Данные
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Email newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Email newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Email onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Email query()
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereSentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereTextPlain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Email withoutTrashed()
 */
	class Email extends \Eloquent {}
}

namespace App\Models\System{
/**
 * App\Models\System\FileUpload
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $path
 * @property string $original_name
 * @property string|null $model
 * @property int|null $model_id
 * @property string|null $field_key
 * @property int $used Использован ли файл где-то
 * @property string|null $created_at
 * @property string|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FileUpload newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FileUpload newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FileUpload query()
 * @method static \Illuminate\Database\Eloquent\Builder|FileUpload whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileUpload whereFieldKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileUpload whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileUpload whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileUpload whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileUpload whereOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileUpload wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileUpload whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileUpload whereUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileUpload whereUserId($value)
 */
	class FileUpload extends \Eloquent {}
}

namespace App\Models\System{
/**
 * App\Models\System\Localization
 *
 * @property int $id
 * @property int|null $section_id
 * @property string $key Полный ключ (вместе с разделом)
 * @property array $text Какой текст указан (на разных языках)
 * @property string $type Как редактируется в админке
 * @property array|null $name Название (подсказка) для админа
 * @property string $lang_file Файл где будет сохранена локализация
 * @property int|null $helper_id
 * @property int|null $helper_section_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\System\LocalizationSection|null $section
 * @method static \Illuminate\Database\Eloquent\Builder|Localization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Localization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Localization onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Localization query()
 * @method static \Illuminate\Database\Eloquent\Builder|Localization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Localization whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Localization whereHelperId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Localization whereHelperSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Localization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Localization whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Localization whereLangFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Localization whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Localization whereSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Localization whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Localization whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Localization whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Localization withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Localization withoutTrashed()
 */
	class Localization extends \Eloquent {}
}

namespace App\Models\System{
/**
 * App\Models\System\LocalizationSection
 *
 * @property int $id
 * @property int|null $section_id
 * @property array|null $name Название раздела для админа на разных языках
 * @property string $key
 * @property int|null $helper_id
 * @property int|null $helper_section_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read string|null $breadcrumb_name
 * @property-read LocalizationSection|null $section
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LocalizationSection> $sections
 * @property-read int|null $sections_count
 * @method static \Illuminate\Database\Eloquent\Builder|LocalizationSection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LocalizationSection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LocalizationSection onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LocalizationSection query()
 * @method static \Illuminate\Database\Eloquent\Builder|LocalizationSection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LocalizationSection whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LocalizationSection whereHelperId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LocalizationSection whereHelperSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LocalizationSection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LocalizationSection whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LocalizationSection whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LocalizationSection whereSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LocalizationSection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LocalizationSection withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LocalizationSection withoutTrashed()
 */
	class LocalizationSection extends \Eloquent {}
}

namespace App\Models\System{
/**
 * App\Models\System\Role
 *
 * @property int $id
 * @property string $name
 * @property string $key Уникальный ключ роли.
 * @property array|null $access
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Role withoutTrashed()
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $email
 * @property string $avatar
 * @property \Illuminate\Support\Carbon|null $last_active_at Время последней активности пользователя
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\System\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastActiveAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail {}
}

