<?php

namespace App\Services\System;

use App\Extra\Services\Service;
use Illuminate\Support\Facades\Auth;

class AdminService extends Service
{

    /**
     * Получить различную информацию для клиента с сервера
     */
    public function getInformationForClient( array $request ): array
    {

        if( !Auth::check() )
            return [];

        return [
            'new_client_events' => events()->getNewClientEvents( $request['last_client_event_id'] ),
            'last_client_event_id' => events()->getLastClientId(),
            'csrf_token' => csrf_token(),
            'navigation_counts' => $this->getNavigationCounts(),
            'isImpersonating' => users()->isImpersonating()
        ];

    }



    /**
     * Получить кол-во новых "событий" для меню
     * Ключ - название, которое передаем в массив ссылок
     * Значение - кол-во новых "событий" для этого пункта меню
     */
    public function getNavigationCounts(): array
    {
        return cacheService()->oneLoad('AdminNavigationCounts', function (){

            return [
                'fail_emails' => emails()->getCountFails(),
            ];

        });
    }


    /**
     * Регистрация навигации Over Top - верхнее меню
     */
    public function navRegisterOverTop(): array
    {

        return $this->navRegister('over_top', [

        ]);

    }


    /**
     * Регистрация навигации Top - верхнее меню
     */
    public function navRegisterTop(): array
    {

        return $this->navRegister('top', [

        ]);

    }


    /**
     * Регистрация навигации Top Profile - ссылки в профиле (верхнее меню, при клике на аватар)
     */
    public function navRegisterTopProfile(): array
    {

        return $this->navRegister('top_profile', [
            [
                'text' => ' Профиль',
                'href' => route('admin.profile.read'),
                'active_route_prefix' => 'admin.profile',
                'icon' => 'far fa-user'
            ],
            [
                'text' => 'Dev Tools',
                'href' => route('admin.dev_tools.index'),
                'active_route_prefix' => 'admin.dev',
                'icon' => 'fa fa-code',
                'access' => 'dev.*'
            ],
            [
                'text' => 'Выйти',
                'href' => '#logout',
                'icon' => 'fas fa-sign-out-alt',
                'hr_before' => true
            ]
        ]);

    }


    /**
     * Регистрация навигации Left - левая боковая колонка
     */
    public function navRegisterLeft(): array
    {

        return $this->navRegister('left', [
            [
                'text' => 'Пользователи',
                'href' => route('admin.users.browse'),
                'active_route_prefix' => 'admin.user',
                'icon' => 'fa fa-users',
                'access' => 'users.*'
            ],
            [
                'text' => 'Платежи',
                'href' => route('admin.payments.browse'),
                'active_route_prefix' => 'admin.payments',
                'icon' => 'fa fa-credit-card',
                'access' => 'payments.*'
            ],
            [
                'text' => 'Промокоды',
                'href' => route('admin.promocodes.browse'),
                'active_route_prefix' => 'admin.promocodes',
                'icon' => 'fa fa-tags',
                'access' => 'promo_codes.*'
            ],
            [
                'text' => 'Логи',
                'href' => route('admin.logs.browse'),
                'active_route_prefix' => 'admin.logs',
                'icon' => 'fa fa-history',
                'access' => 'logs.*'
            ],
            [
                'text' => 'Настройки',
                'href' => route('admin.settings.index'),
                'active_route_prefix' => settings(false)->getAllRoutesPrefixes(),
                'icon' => 'fa fa-gear',
                'access' => 'settings.*'
            ],
            [
                'text' => 'Emails',
                'href' => route('admin.emails.browse'),
                'active_route_prefix' => 'admin.email',
                'icon' => 'fa fa-envelope',
                'access' => 'emails.*',
                'count_key' => 'fail_emails'
            ],
            [
                'text' => 'Локализация',
                'href' => route('admin.localization.browse'),
                'active_route_prefix' => 'admin.localization',
                'icon' => 'fa fa-language',
                'access' => 'localization.*'
            ]
        ]);

    }


    /**
     * Проверяет есть ли ссылки в навигации
     */
    public function isNavRegistered( string $nav ): bool
    {

        return (bool)count($this->getNavRegistered($nav));

    }


    /**
     * Получить ссылки для меню
     */
    public function getNavRegistered( string $nav ): array
    {

        $func = 'navRegister'.str()->camel($nav);

        return $this->{$func}();

    }


    /**
     * Регистрация навигации
     */
    private function navRegister( string $nav_key, array $links ): array
    {

        return navigation()->linksRegister($nav_key, $links, function(){

            return $this->getNavigationCounts();

        });

    }



    /**
     * Проверка - может ли просматривать /admin/
     */
    public function canSee(): bool
    {

        if( !Auth::check() )
            return false;

        //Если есть хоть какой-то доступ у юзера, считаем что он может (теоретически) просматривать (этот метод не дает право ему просматривать, просто проверяет, может ли в теории быть доступ к разделу)
        if( roles()->checkAccess('*') )
            return true;

        return false;

    }



    /**
     * Проверка открыта ли какая-та страница /admin/
     */
    public function isRouteOpen(): bool
    {
        return str(request()->path())->startsWith('admin');
    }



    /**
     * Проверка - может ли авторизованный юзер смотреть /admin/, и открыта ли сейчас /admin/
     */
    public function checkCanSeeAndRouteOpen(): bool
    {

        return $this->canSee() && $this->isRouteOpen();

    }


    /**
     * Вывести информацию об объекте в таблицу.
     * Нужно для обновления таблиц сущностей при добавлении/редактировании сущности.
     */
    public function getHTMLTableRow($essence_id, $type, $template = null, $check_access = true ): string
    {

        $singular = null;
        $plural = null;
        $func_name = null;


        //Здесь может быть ручная обработка конкретного типа, если автоматически не срабатывает в конкретном случае
        //if ($type == 'some_type') ...


        //Пытаемся автоматически обработать тип и подгрузить нужную строку
        try {

            if( !$singular )
                $singular = str($type)->singular()->lower()->replace('-', '_')->toString();

            if( !$plural )
                $plural = str($singular)->plural()->replace('-', '_')->toString();

            if( !$func_name )
                $func_name = str($plural)->camel()->toString();

            //Проверяем есть ли доступ просматривать сущность
            if( $check_access && !roles()->checkAccess($plural.'.browse') )
                return false;

            if( is_null($template) )
                $template = 'admin.app.'.$plural.'.components.table-row';

            return view($template, [
                $singular => $func_name()->getOneByID( $essence_id )
            ])->render();


        } catch ( \Throwable $exception ){

            logger()->error($exception->getMessage().' in '.$exception->getFile().':'.$exception->getLine(), [
                'essence_id' => $essence_id,
                'type' => $type,
                'template' => $template,
                'check_access' => $check_access
            ]);
            error('getHTMLTableRow ERROR:' . $exception->getMessage().' type: '. $type);

        }

    }


    /**
     * Pagination Items
     */
    public function paginate( $items, $paginator_view = 'admin.components.paginate' ){

        return $items->appends(request()->except(['ajax','_']))->links($paginator_view);

    }



    /**
     * Buttons
     * Returns HTML
     */
    public function buttons( array $buttons ): string
    {

        if( array_key_first($buttons) != 0 )
            $buttons = [$buttons];

        $prepared = [];

        foreach( $buttons as $button ){

            //Если это массив кнопок
            if( array_key_first($button) == 0 ){


                $group_buttons = [];

                foreach( $button as $btn )
                    $group_buttons = array_merge($group_buttons, $this->prepareBtnData($btn));


                if( count($group_buttons) )
                    $prepared[] = $group_buttons;


            //Если это одна кнопка
            }else{

                $prepared = array_merge($prepared, $this->prepareBtnData($button));

            }



        }

        return view('admin.components.buttons', [
            'buttons' => $prepared
        ])->render();

    }


    /**
     * Prepare Data for buttons
     */
    protected function prepareBtnData( array $button ): array
    {

        $data = [];


        //Если нет доступа к этой кнопке
        if( isset($button['access']) && !roles()->checkAccess($button['access']) ){

            //Но при этом, если есть доступ к кнопкам dropdown - то мы их должны вынести
            if( isset($button['dropdown']) ){

                foreach( $button['dropdown'] as $dropdown_btn ){

                    if( roles()->checkAccess($button['access'] ?? '*') )
                        $data[] = $this->prepareBtnArray($dropdown_btn);

                }

            }


        //Если доступ к этой кнопке есть
        }else{

            $data[] = $this->prepareBtnArray($button);

        }


        return $data;

    }



    /**
     * Prepare Data for button
     */
    protected function prepareBtnArray( array $button, bool $is_dropdown = false ): array
    {

        $btn_attributes = [
            'href' => $button['href'] ?? '#'
        ];


        if( $is_dropdown ){

            $btn_attributes['class'] = 'dropdown-item';

        }else{

            $btn_attributes['class'] = 'btn btn-' . ($button['style'] ?? 'primary');

        }


        if( isset($button['offcanvas-href']) ){

            $btn_attributes['data-offcanvas-href'] = $button['offcanvas-href'];


        }elseif( isset($button['modal-href']) ){

            $btn_attributes['data-modal-href'] = $button['modal-href'];

        }

        if( isset($button['class']) )
            $btn_attributes['class'] = $button['class'];


        if( isset($button['data-size']) )
            $btn_attributes['data-size'] = $button['data-size'];


        $data = [
            'icon' => $button['icon'] ?? null,
            'text' => $button['text'] ?? null,
            'attributes' => $btn_attributes
        ];


        //Если есть dropdown кнопки
        if( isset($button['dropdown']) ){

            $dropdown = [];

            foreach( $button['dropdown'] as $btn ){

                if( roles()->checkAccess($btn['access'] ?? '*') )
                    $dropdown[] = $this->prepareBtnArray($btn, true);

            }

            if( count($dropdown) ) {
                $data['dropdown'] = $dropdown;

                $data['dropdown_text'] = $button['dropdown_text'] ?? null;

                if( $data['dropdown_text'] )
                    $data['dropdown_text'] .= ' ';

            }

        }


        return $data;

    }



    /**
     * Может ли пользователь менять тему
     */
    public bool $can_change_theme = true;



    /**
     * Стандартная тема
     */
    public string $default_theme = 'light';



    /**
     * Возможные темы и путь к стилям
     * @throws \Exception
     */
    public function getAvailableThemes(): array
    {
        return [
            'light' => [
                'name' => 'Светлая',
                'css_path' => mix('/assets/admin/css/themes/light.css')->toHtml()
            ],
            'dark' => [
                'name' => 'Темная',
                'css_path' => mix('/assets/admin/css/themes/dark.css')->toHtml()
            ]
        ];
    }


    /**
     * Получить текущую тему для загрузки
     * @throws \Exception
     */
    public function getTheme(): array
    {

        $themes = $this->getAvailableThemes();

        if( !$this->can_change_theme ) {

            $current_theme = $this->default_theme;

        }else{

            $current_theme = $_COOKIE['current_theme'] ?? $this->default_theme;

        }

        $theme = $themes[$current_theme] ?? array_shift($themes);

        return [
            'key' => $current_theme,
            'css_path' => $theme['css_path'],
            'name' => $theme['name']
        ];

    }

}
