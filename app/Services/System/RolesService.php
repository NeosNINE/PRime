<?php

namespace App\Services\System;

use App\Extra\Services\Service;
use App\Extra\Services\Traits\ServiceTrait;
use App\Models\System\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;


class RolesService extends Service
{

    use ServiceTrait;

    /**
     * Список ключей ролей у которых есть доступ ко всем функциям.
     */
    public function getAllAccessKeys(): array
    {

        return [
            'super_admin',
            'admin',
            'developer'
        ];

    }




    /**
     * Получить список возможных доступов для роли.
     */
    public function getAccesses(): array
    {

        $accesses = [];

        $accesses['users'] = $this->getBREADAccesses('Пользователи', 'users', [
            'browse' => 'Просмотр списка пользователей',
            'read' => 'Просмотр подробной информации о пользователе',
            'add' => 'Добавление новых пользователей',
            'edit' => 'Редактирование пользователей',
            'delete' => 'Удаление пользователей',
            'impersonate' => 'Возможность авторизоваться за другого пользователя'
        ]);

        $accesses['providers'] = $this->getBREADAccesses('Провайдеры услуг', 'providers', [
            'browse' => 'Просмотр списка провайдеров',
            'read' => 'Просмотр информации о провайдере',
            'add' => 'Добавление провайдеров',
            'edit' => 'Редактирование провайдеров',
        ]);

        $accesses['services'] = $this->getBREADAccesses('Услуги', 'services', [
            'browse' => 'Просмотр списка услуг',
            'read' => 'Просмотр информации об услуге',
            'add' => 'Добавление услуг',
            'edit' => 'Редактирование услуг',
            'delete' => 'Удаление услуг',
        ]);

        $accesses['orders'] = [
            'name' => 'Заказы',
            'accesses' => [
                'orders.browse' => ['name' => 'Просмотр списка заказов'],
                'orders.read' => ['name' => 'Просмотр деталей заказа', 'if_specified' => 'orders.browse'],
                'orders.edit' => ['name' => 'Управление статусами ручных заказов', 'if_specified' => 'orders.read'],
                'orders.export' => ['name' => 'Экспорт заказов', 'if_specified' => 'orders.browse'],
            ],
        ];

        $accesses['roles'] = $this->getBREADAccesses('Роли', 'roles', [
            'browse' => 'Просмотр списка ролей',
            'add' => 'Добавление новых ролей',
            'edit' => 'Редактирование ролей',
            'delete' => 'Удаление ролей'
        ]);

        $accesses['payments'] = $this->getBREADAccesses('Платежи', 'payments', [
            'browse' => 'Просмотр списка платежей',
            'read' => 'Просмотр деталей платежа',
            'accept' => 'Принятие платежа',
            'refund' => 'Возврат платежа',
            'balance' => 'Изменение баланса пользователя'
        ]);

        $accesses['promo_codes'] = $this->getBREADAccesses('Промокоды', 'promo_codes', [
            'browse' => 'Просмотр списка промокодов',
            'read' => 'Просмотр деталей промокода',
            'add' => 'Создание промокода',
            'edit' => 'Редактирование промокода',
            'delete' => 'Удаление промокода'
        ]);

        $accesses['logs'] = $this->getBREADAccesses('Логи', 'logs', [
            'browse' => 'Просмотр логов'
        ]);

//        $accesses['emails'] = $this->getBREADAccesses('Email сообщения', 'emails', [
//            'browse' => 'Просмотр списка сообщений',
//            'read' => 'Просмотр подробной информации о сообщении',
//            'resend' => 'Повторная отправка сообщения',
//            'delete' => 'Удаление сообщения'
//        ]);

//        $accesses['localization'] = $this->getBREADAccesses('Локализация', 'localization', [
//            'manage' => 'Управление локализацией'
//        ]);


        $settings_accesses = $this->getSettingsAccesses();
        if( count($settings_accesses) )
            $accesses['settings'] = $settings_accesses;

        return $accesses;

    }



    /**
     * Задать стандартный BREAD access для сущности
     * @param string $essence_name - название сущности во множественном числе. Пример: Пользователи
     * @param string $essence_key - ключ сущности во множественном числе. Пример: users
     * @param array $names - массив названий/описаний действий. С ключом [0] - просмотр списка, [1] - просмотр подробной информации, [2] - добавление, [3] - редактирование, [4] - удаление
     * @return array
     */
    private function getBREADAccesses( string $essence_name, string $essence_key, array $names ): array
    {

        $accesses = [
            'name' => $essence_name,
            'accesses' => []
        ];

        foreach( $names as $name_key => $name ){

            $arr = [
                'name' => $name
            ];

            if( $name_key != 'browse' && isset($names['browse']) )
                $arr['if_specified'] = $essence_key . '.' . 'browse';

            $accesses['accesses'][ $essence_key . '.' . $name_key ] = $arr;

        }

        return $accesses;

    }



    /**
     * Получить список прав доступа для Настроек
     */
    public function getSettingsAccesses(): array
    {

        $accesses = [];
        $sections = settings(false)->getAllSections();

        foreach( $sections as $section_key => $section_val ){

            //Если это секция не mange (например route) - пропускаем
            if( !isset($section_val['manage']) )
                continue;

            $accesses['settings.'.$section_key.'.read'] = [ 'name' => 'Просмотр: "'.$section_val['name'].'"' ];
            $accesses['settings.'.$section_key.'.edit'] = [ 'name' => 'Редактирование: "'.$section_val['name'].'"', 'if_specified' =>  'settings.'.$section_key.'.read'];

        }

        if( !count($accesses) )
            return [];


        return [
            'name' => 'Настройки',
            'accesses' => $accesses
        ];

    }



    /**
     * Получить массив возможных ключей доступа
     */
    public function getAccessesKeys(): array
    {

        $keys = [];

        foreach( $this->getAccesses() as $essence_data ){

            foreach( $essence_data['accesses'] as $access_key => $access_data ){

                $keys[] = $access_key;

            }

        }

        return $keys;

    }


    /**
     * Получить массив с ключами доступами, где значение будут название доступа.
     * @param Role $role
     * @param string $group_key - если указано, получаем доступы только определенной группы
     * @param bool $remove_commas - Удаляет точки из конца названий, чтобы красиво выводить labels
     * @return array
     */
    public function getAccessesNames( Role $role, string $group_key = '', bool $remove_commas = true ): array
    {

        $names = [];

        foreach( $this->getAccesses() as $essence_key => $essence_data ){

            if( $group_key && $group_key != $essence_key )
                continue;

            foreach( $essence_data['accesses'] as $access_key => $access_data ){


                //Если у роли нет доступа к этой возможности - пропускаем
                if( !in_array($access_key, (array)$role->access) )
                    continue;

                if( $remove_commas && Str::endsWith($access_data['name'], '.') )
                    $access_data['name'] = substr($access_data['name'], 0, -1);

                $names[$access_key] = $access_data['name'];

            }

        }

        return $names;

    }



    /**
     * Получить список групп доступа
     */
    public function getGroupsAccesses(): array
    {

        $groups = [];

        foreach( $this->getAccesses() as $group_key => $essence_data )
            $groups[$group_key] = $essence_data['name'];


        return $groups;

    }


    /**
     * Проверить есть ли доступ у роли ко всей группе прав
     */
    public function checkGroupAccess( Role $role, string $group_key ): bool
    {

        foreach( $this->getAccesses()[$group_key]['accesses'] ?? [] as $access_key => $access_name ){

            if( !in_array($access_key, $role->access ?? []) )
                return false;

        }

        return true;

    }



    /**
     * Подготовить данные для добавления
     */
    protected function addDataPrepare( array $data ): array
    {

        $data['key'] = Str::snake($data['key']);

        return $data;

    }



    /**
     * Подготовить данные для редактирования
     */
    protected function editDataPrepare( array $data ): array
    {

        //Делаем так, чтобы редактировать ключ было нельзя
        unset($data['key']);

        return $data;

    }


    /**
     * Подготовка перед редактированием
     */
    protected function beforeEdit( array $data, Role $role ): void
    {

        //Если не указаны доступы (не один чекбокс не отмечен - проставляем NULL)
        if( !isset($data['access']) )
            $role->access = NULL;

    }


    /**
     * Подготовка к удалению
     */
    protected function beforeDelete( Role $role ): void
    {

        if( in_array($role->key, roles()->getAllAccessKeys()) )
            abort(403, 'Эту роль невозможно удалить.');

    }



    /**
     * Валидация при сохранении
     */
    public function saveValidate( array $data ): void
    {

        Validator::make( $data, [
            'name' => 'required',
            'access' => ['array', Rule::in( $this->getAccessesKeys() )]
        ])->validate();

    }




    /**
     * Валидация при добавлении
     */
    public function addValidate( array $data ): void
    {

        Validator::make( $data, [
            'key' => 'required|unique:roles'
        ])->validate();

    }



    /**
     * Получить все доступы у пользователя (из всех его ролей)
     */
    public function getUserAccessesKeys( User $user = null,  $toJson = false  ): string|array
    {
        $user = users()->getUser($user);

        if( !$user )
            return [];

        $accesses = [];

        foreach( $user->roles as $role ){

            if( in_array($role->key, $this->getAllAccessKeys()) ){

                $accesses = $this->getAccessesKeys();
                break;

            }

            $accesses = array_merge($accesses, $role->access ?? []);

        }

        $accesses = array_unique($accesses);

        if( $toJson )
            $accesses = json_encode($accesses);

        return $accesses;

    }



    /**
     * Проверить права доступа у пользователя
     * @param $access_keys - string|array - если передан массив, то возвращает true, только если ВСЕ нужные доступы имеются
     * В конце ключа можно прописывать .* (например users.*), это означает, чтобы вернулся true, должен быть доступ ко всем ключам users (users.browse, users.add, users.edit и т.д.)
     * Если передать * вернет true, если есть хоть какой-то доступ
     * @param bool $all_accesses_mode - если передан параметр true, то возвращает true, только если ВСЕ нужные доступы имеются. Если false - то вернет true если хотя бы один из доступов имеется
     * @param bool $considerImpersonator - если true, то будут браться доступы админа impersonator (если админ под юзером авторизован)
     */
    public function checkAccess( string|array $access_keys, User $user = null, bool $all_accesses_mode = false, bool $considerImpersonator = true ): bool
    {

        //Скрипт запущенный из консоли имеет все доступы
        if( app()->runningInConsole() )
            return true;

        $cache_key = 'checkAccess' . implode('',(array)$access_keys) . ($user->id ?? 0) . $all_accesses_mode;

        return cacheService()->oneLoad($cache_key, function () use ($access_keys, $user, $all_accesses_mode) {

            $user = users()->getUser($user);

            if( !$user )
                return false;


            //Для разработчика будут доступны все функции
            //А для админа будет доступно только то что прописано в $this->getAccesses()
            if( $this->isDeveloper($user) )
                return true;


            //Проверка есть ли хотя бы один какой-то доступ
            if( $access_keys == '*' ){

                if( count($this->getUserAccessesKeys($user)) )
                    return true;

                return false;

            }


            if( !is_array($access_keys) )
                $access_keys = [$access_keys];

            $pass_accesses = [];

            foreach( $access_keys as $key => $access_key ){

                if( str($access_key)->endsWith('.*') ){

                    $access_key = str_replace('.*', '', $access_key);

                    foreach( $this->getAccessesKeys() as $k ){

                        if( str($k)->startsWith($access_key) )
                            $access_keys[] = $k;

                    }

                    unset($access_keys[$key]);

                }

            }

            $access_keys = array_unique($access_keys);

            if( !count($access_keys) )
                return false;

            foreach( array_intersect($this->getUserAccessesKeys($user), $access_keys) as $pass_access ){

                if( $all_accesses_mode ){

                    $pass_accesses[$pass_access] = true;

                }else{

                    return true;

                }

            }

            if( count($pass_accesses) == count($access_keys) )
                return true;

            return false;


        });

    }


    /**
     * Проверить права доступа у пользователя (если нет выкидывает abort)
     */
    public function checkAccessWithAbort( string|array $access_keys, User $user = null ): void
    {

        $user = users()->getUser($user, true, false);

        if( !$user )
            redirect()->route('login', ['backTo' => urlencode(request()->getRequestUri())])->throwResponse();


        if( !$this->checkAccess($access_keys, $user) ) {

            if( users()->isImpersonating() && !roles()->isAdmin($user) ) {

                abort(420, 'Вы авторизованны под другим юзером.');

            }elseif( admin()->checkCanSeeAndRouteOpen() ){

                abort(403, 'У Вас нет доступа');

            }else{

                abort(404);

            }

        }

    }


    /**
     * Является ли юзер суперадмином
     */
    public function isSuperAdmin( User $user = null ): bool
    {

        $user = users()->getUser($user);

        if( !$user )
            return false;

        if( in_array( 'super_admin', $user->roles->pluck('key')->toArray()) )
            return true;

        return false;

    }



    /**
     * Является ли юзер обычным админом (если является Super Admin, но не является обычным админом - вернет FALSE)
     * Если юзер Super Admin - вернет FALSE
     */
    public function isUsualAdmin( User $user = null ): bool
    {

        if( $this->isSuperAdmin($user) )
            return false;

        $user = users()->getUser($user);

        if( !$user )
            return false;

        if( in_array( 'admin', $user->roles->pluck('key')->toArray()) )
            return true;

        return false;

    }



    /**
     * Является ли юзер админом (если юзер является Super Admin, этот метод так же вернет TRUE)
     */
    public function isAdmin( User $user = null ): bool
    {

        if( $this->isDeveloper($user) )
            return true;

        if( $this->isUsualAdmin($user) || $this->isSuperAdmin($user) )
            return true;

        return false;

    }


    /**
     * Проверяем имеет ли пользователь хоть какие-то доступы к админке
     * @return bool
     */
    public function isHasAnyAdminAccess( User $user = null ): bool
    {

        return $this->checkAccess('*', $user);

    }



    /**
     * Является ли юзер разработчиком
     */
    public function isDeveloper( User $user = null ): bool
    {

        $user = users()->getUser($user);

        if( !$user )
            return false;

        if( in_array( 'developer', $user->roles->pluck('key')->toArray()) )
            return true;

        return false;

    }



    /**
     * Получить первый доступный (к которому есть доступ у юзера) URL для переадресации
     */
    public function getFirstAccessURL(): string
    {

        if( !Auth::check() || !count(Auth::user()->roles) )
            abort(404);

        //Перебираем все пункты меню и перенаправляем на первый доступный пункт меню
        foreach( ['left', 'top', 'top_profile', 'over_top'] as $nav_key ){

            foreach( admin()->getNavRegistered($nav_key) as $link ){

                if( $link['href'] == '' || $link['href'] == '#' )
                    continue;

                if( !isset($link['access']) || roles()->checkAccess($link['access'], all_accesses_mode: $link['all_accesses_mode']) )
                    return $link['href'];

            }

        }

        abort(404);

    }



    /**
     * Проверка, можем ли мы изменить роли у юзера
     */
    public function canEditRoles( User $edited_user = null, Role $role = null ): bool
    {

        if( $edited_user ){

            if( Auth::user()->isSuperAdmin() && $edited_user->id == Auth::id() )
                return true;

            if( $edited_user->isSuperAdmin() )
                return false;

            if( (($role && $role->key != 'super_admin') || !$role) && Auth::user()->isAdmin() && $edited_user->id == Auth::id() )
                return true;

            if( Auth::user()->isSuperAdmin() && !$edited_user->isSuperAdmin() )
                return true;

            if( Auth::user()->isAdmin() && $edited_user->isAdmin() )
                return false;

        }else{

            if( Auth::user()->isSuperAdmin() )
                return true;

        }

        if( $role && $role->key == 'super_admin' )
            return false;

        if( Auth::user()->isAdmin() )
            return true;


        return false;

    }

}
