<?php

namespace App\Services\System;

class NavigationService
{

    /**
     * Регистрации ссылок для навигации
     * Возможные параметры:
     * [change_text] - (bool) менять ли текст меню при клике на элементы sub-nav (default: false)
     * [active_route_prefix] - (string|array) префикс routes, при котором делаем меню активным, можно передавать строку или массив из префиксов
     * [sub-nav] - (array) массив с дочерними пунктами меню
     * [count_key] - (string) ключ для проставления кол-ва "событий" в пункт меню из функции @param callable $count_func
     * [_blank] - (bool) если true, то ссылка будет открываться в новом окне
     * [not_ajax] - (bool) если true, то ссылка будет открываться без AJAX загрузки контента
     * [open_to_left] - (bool) sub nav будет открываться слева (работает для 2 и выше уровня вложенности), по умолчанию открывается справа. Это только для верхнего меню. Остальные меню игнорируют этот параметр
     * [hr_before] - (bool) перед пунктом навигации будет проведена разделительная линия
     * [extra_css_classes] - (string|array) - CSS классы, которые будут применены к ссылке. Если один класс, то можно указывать в виде строки, если несколько - то массив
     * [access] - (string|array) - список ключей доступа, которые должны быть у юзера для того, чтобы этот пункт меню показался
     * [all_accesses_mode] - (bool) - мод, который применяется при проверке доступа (default: false). Если передан параметр true, то покажет ссылку, только если ВСЕ нужные доступы имеются. Если false - то покажет ссылку, если хотя бы один из доступов имеется
     * [offcanvas] - (array) - если передан, то тогда будет открываться как offcanvas окно (ключ offcanvas а в значения передаем массив пустой или с данными размера и т.д.
     * [modal] - (array) - если передан, то тогда будет открываться как modal окно (ключ modal а в значения передаем массив пустой или с данными размера и т.д.
     */
    public function linksRegister( string $nav_key, array $links, callable $count_func ): array
    {

        foreach( $links as $key => $link ){

            if( !isset($link['change_text']) )
                $links[$key]['change_text'] = 'false';

            if( isset($link['change_text']) && $link['change_text'] )
                $links[$key]['change_text'] = 'true';

            if( isset($link['change_text']) && !$link['change_text'] )
                $links[$key]['change_text'] = 'false';


            if( isset($link['active_route_prefix']) && !is_array($link['active_route_prefix']) )
                $links[$key]['active_route_prefix'] = [$link['active_route_prefix']];


            if( isset($link['sub-nav']) ) {

                $links[$key]['sub-nav'] = $this->linksRegister($nav_key, $link['sub-nav'], $count_func);
                $links[$key]['sub-nav_active_routes_prefix'] = $this->getSubNavActiveRoutesPrefix($link);

                $links[$key]['href'] = '#';
            }


            if( isset($link['count_key']) ){

                $count = $count_func()[$link['count_key']] ?? 0;

                if( !is_integer($count) )
                    error('Count should be an integer value');

                $links[$key]['count'] = $count;

            }

            if( !isset($link['all_accesses_mode']) )
                $links[$key]['all_accesses_mode'] = false;


            if( !isset($link['extra_css_classes']) )
                $links[$key]['extra_css_classes'] = [];


            if( is_string($links[$key]['extra_css_classes']) )
                $links[$key]['extra_css_classes'] = [$link['extra_css_classes']];


            if( $nav_key == 'top_profile' )
                $links[$key]['open_to_left'] = true;


        }

        return $links;

    }


    /**
     * Получить список префиксов активного меню для sub-nav
     */
    public function getSubNavActiveRoutesPrefix( array $link ): array
    {
        $sub_nav_active_routes_prefix = [];

        array_walk_recursive($link['sub-nav'], function ($val, $key) use (&$sub_nav_active_routes_prefix){
            if( $key == 'active_route_prefix' ) {

                if( is_array($val) ){

                    $sub_nav_active_routes_prefix = array_merge($sub_nav_active_routes_prefix, $val);

                }else{

                    $sub_nav_active_routes_prefix[] = $val;

                }

            }
        });

        return $sub_nav_active_routes_prefix;
    }


    /**
     * Проверить, является ли пункт меню активный сейчас
     */
    public function isActive( array $link ): bool
    {

        //Если есть подменю - проверяем, если хотя бы один из пунктов подменю активный - значит и родительский пункт активный
        if( isset($link['sub-nav']) ){

            foreach( $link['sub-nav'] as $sub_link ){

                if( $this->isActive($sub_link) )
                    return true;

            }

        }

        if( isset($link['active_route_prefix']) && str()->startsWith(\Route::currentRouteName(), $link['active_route_prefix']) )
            return true;

        return false;

    }


    /**
     * Доступен ли пункт меню только для разработчика
     */
    public function isAvailableOnlyForDev( array $link ): bool
    {
        //Если есть подменю - проверяем, если все пункты подменю для разработчика только - то значит и родительский пункт только для разработчиков
        if( isset($link['sub-nav']) ){

            $sub_only_for_dev = true;

            foreach( $link['sub-nav'] as $sub_link ){

                if( !$this->isAvailableOnlyForDev($sub_link) )
                    $sub_only_for_dev = false;

            }

            return $sub_only_for_dev;

        }


        if( !isset($link['access']) )
            return false;


        $check_access = str($link['access'])->replace('.*', '.');

        foreach( roles()->getAccessesKeys() as $access_key ){

            if( str($access_key)->startsWith($check_access) )
                return false;

        }

        return true;

    }


    /**
     * Проверить есть ли доступ к ссылке
     */
    public function checkAccess( array $link ): bool
    {

        if( !isset($link['access']) )
            return true;


        //Если есть подменю - проверяем, если все пункты подменю не доступны - то и родительское не выводим
        if( isset($link['sub-nav']) ){

            $has_access = false;

            foreach( $link['sub-nav'] as $sub_link ){

                if( $this->checkAccess($sub_link) )
                    $has_access = true;

            }

            return $has_access;

        }

        return roles()->checkAccess($link['access'], all_accesses_mode: $link['all_accesses_mode']);

    }


    /**
     * Получить аттрибуты ссылки
     */
    public function getAttributes( array $link ): string
    {

        if( isset($link['offcanvas']) ){

            $attrs['href'] = 'data-offcanvas-href="'.$link['href'].'" href="#"';

            foreach( $link['offcanvas'] as $key => $value ){

                $attrs[] = $key.'="'.$value.'"';

            }

        }elseif( isset($link['modal']) ){

            $attrs['href'] = 'data-modal-href="'.$link['href'].'" href="#"';

            foreach( $link['modal'] as $key => $value ){

                $attrs[] = $key.'="'.$value.'"';

            }

        }else{

            $attrs['href'] = 'href="'.$link['href'].'"';

        }

        if( isset($link['_blank']) )
            $attrs[] = 'target="_blank"';

        return implode(' ', $attrs);

    }



    /**
     * Получить CSS классы для ссылки
     */
    public function getCSSClasses( array $link ): string
    {

        $classes = [];

        if( $this->isActive($link) )
            $classes[] = 'active';

        if( isset($link['not_ajax']) )
            $classes[] = 'not-ajax';

        foreach( $link['extra_css_classes'] as $class )
            $classes[] = $class;

        $classes = array_unique( $classes );

        return implode(' ', $classes);
    }

}
