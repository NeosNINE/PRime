<?php


    /**
     *  ВНИМАНИЕ! В этом файле функции не стоит добавлять вручную. Этот файл автоматически генерируются
     *  с помощью команды php artisan revered:helperRefresh и изменения в данном файле могут быть перетерты.
     *  Добавляйте нужные функции в файл  App\Extra\Helpers\HelperClass.php
     *  и далее запускайте команду php artisan revered:helperRefresh
     *  Функции-хелперы для сервисов формируются автоматически, их не нужно добавлять в класс HelperClass
     */






    use App\Extra\Helpers\HelperClass;


    /**
     *  Поверка существования функции
     */
    function helperFunctionCheck( $func ): bool
    {

        if( function_exists($func) )
            dd("The helper function '$func' already exists.");

        return true;
    }


    /**
    *   Инициализация хелпера
    */
    if( helperFunctionCheck('helperClass') ){

        function helperClass() : HelperClass
        {
            return HelperClass::getInstance();
        }

    }
    


    if( helperFunctionCheck('notifyUser') ){

        function notifyUser( App\Models\User $user, string $title, string $text, ?string $url = null, string $icon = "fas fa-info-circle", string $type = "info" ) : void
        {
            helperClass()->notifyUser( $user, $title, $text, $url, $icon, $type );
        }

    }



    if( helperFunctionCheck('getUserUnreadNotificationsCount') ){

        function getUserUnreadNotificationsCount( ?App\Models\User $user = null ) : int
        {
            return helperClass()->getUserUnreadNotificationsCount( $user );
        }

    }



    if( helperFunctionCheck('lang') ){

        function lang( $key = null, $replace = [], $locale = null ) : ?string
        {
            return helperClass()->lang( $key, $replace, $locale );
        }

    }



    if( helperFunctionCheck('langText') ){

        function langText( $text, $lang = false ) : ?string
        {
            return helperClass()->langText( $text, $lang );
        }

    }



    if( helperFunctionCheck('langTextOrNull') ){

        function langTextOrNull( $text, $lang = false ) : ?string
        {
            return helperClass()->langTextOrNull( $text, $lang );
        }

    }



    if( helperFunctionCheck('getRoutesListByName') ){

        function getRoutesListByName( $toJson = true ) : array|string
        {
            return helperClass()->getRoutesListByName( $toJson );
        }

    }



    if( helperFunctionCheck('getKeyTemplateForErrorPage') ){

        function getKeyTemplateForErrorPage() : string
        {
            return helperClass()->getKeyTemplateForErrorPage();
        }

    }



    if( helperFunctionCheck('r') ){

        function r( $if, $true_val, $false_val = "" )
        {
            return helperClass()->r( $if, $true_val, $false_val );
        }

    }



    if( helperFunctionCheck('isProduction') ){

        function isProduction() : bool
        {
            return helperClass()->isProduction();
        }

    }



    if( helperFunctionCheck('isDebug') ){

        function isDebug() : bool
        {
            return helperClass()->isDebug();
        }

    }



    if( helperFunctionCheck('paginateItems') ){

        function paginateItems( $items, $perPage = 20, $page = null, $options = [] ) : Illuminate\Pagination\LengthAwarePaginator
        {
            return helperClass()->paginateItems( $items, $perPage, $page, $options );
        }

    }



    if( helperFunctionCheck('error') ){

        function error( string $msg, int $code = 422 ) : never
        {
            helperClass()->error( $msg, $code );
        }

    }



    if( helperFunctionCheck('getPostMaxSize') ){

        function getPostMaxSize( $as_bytes = false ) : string|int
        {
            return helperClass()->getPostMaxSize( $as_bytes );
        }

    }



    if( helperFunctionCheck('checkConfirmPassword') ){

        function checkConfirmPassword( bool $with_redirect = true, $passwordTimeoutSeconds = null ) : Illuminate\Http\RedirectResponse|bool
        {
            return helperClass()->checkConfirmPassword( $with_redirect, $passwordTimeoutSeconds );
        }

    }



    if( helperFunctionCheck('jsonRedirectResponse') ){

        function jsonRedirectResponse( string $redirect_url ) : void
        {
            helperClass()->jsonRedirectResponse( $redirect_url );
        }

    }







    /**
    * Функции Services
    */

    if( helperFunctionCheck('promoCodes') ){

        /**
        * @param bool $refresh
        * @return \App\Services\PromoCodesService
        */
        function promoCodes( bool $refresh = false ) : \App\Services\PromoCodesService
        {
            static $promo_codes_service_instance = false;

            if( $refresh )
                $promo_codes_service_instance = false;

            if( !$promo_codes_service_instance )
                $promo_codes_service_instance = new \App\Services\PromoCodesService();

            return $promo_codes_service_instance;
        }

    }

    if( helperFunctionCheck('userBalance') ){

        /**
        * @param bool $refresh
        * @return \App\Services\UserBalanceService
        */
        function userBalance( bool $refresh = false ) : \App\Services\UserBalanceService
        {
            static $user_balance_service_instance = false;

            if( $refresh )
                $user_balance_service_instance = false;

            if( !$user_balance_service_instance )
                $user_balance_service_instance = new \App\Services\UserBalanceService();

            return $user_balance_service_instance;
        }

    }

    if( helperFunctionCheck('users') ){

        /**
        * @param bool $refresh
        * @return \App\Services\UsersService
        */
        function users( bool $refresh = false ) : \App\Services\UsersService
        {
            static $users_service_instance = false;

            if( $refresh )
                $users_service_instance = false;

            if( !$users_service_instance )
                $users_service_instance = new \App\Services\UsersService();

            return $users_service_instance;
        }

    }

    if( helperFunctionCheck('admin') ){

        /**
        * @param bool $refresh
        * @return \App\Services\System\AdminService
        */
        function admin( bool $refresh = false ) : \App\Services\System\AdminService
        {
            static $admin_service_instance = false;

            if( $refresh )
                $admin_service_instance = false;

            if( !$admin_service_instance )
                $admin_service_instance = new \App\Services\System\AdminService();

            return $admin_service_instance;
        }

    }

    if( helperFunctionCheck('cacheService') ){

        /**
        * @param bool $refresh
        * @return \App\Services\System\CacheService
        */
        function cacheService( bool $refresh = false ) : \App\Services\System\CacheService
        {
            static $cache_service_instance = false;

            if( $refresh )
                $cache_service_instance = false;

            if( !$cache_service_instance )
                $cache_service_instance = new \App\Services\System\CacheService();

            return $cache_service_instance;
        }

    }

    if( helperFunctionCheck('cfg') ){

        /**
        * @param bool $refresh
        * @return \App\Services\System\CfgService
        */
        function cfg( bool $refresh = false ) : \App\Services\System\CfgService
        {
            static $cfg_service_instance = false;

            if( $refresh )
                $cfg_service_instance = false;

            if( !$cfg_service_instance )
                $cfg_service_instance = new \App\Services\System\CfgService();

            return $cfg_service_instance;
        }

    }

    if( helperFunctionCheck('cleaning') ){

        /**
        * @param bool $refresh
        * @return \App\Services\System\CleaningService
        */
        function cleaning( bool $refresh = false ) : \App\Services\System\CleaningService
        {
            static $cleaning_service_instance = false;

            if( $refresh )
                $cleaning_service_instance = false;

            if( !$cleaning_service_instance )
                $cleaning_service_instance = new \App\Services\System\CleaningService();

            return $cleaning_service_instance;
        }

    }

    if( helperFunctionCheck('currency') ){

        /**
        * @param bool $refresh
        * @return \App\Services\System\CurrencyService
        */
        function currency( bool $refresh = false ) : \App\Services\System\CurrencyService
        {
            static $currency_service_instance = false;

            if( $refresh )
                $currency_service_instance = false;

            if( !$currency_service_instance )
                $currency_service_instance = new \App\Services\System\CurrencyService();

            return $currency_service_instance;
        }

    }

    if( helperFunctionCheck('emails') ){

        /**
        * @param bool $refresh
        * @return \App\Services\System\EmailsService
        */
        function emails( bool $refresh = false ) : \App\Services\System\EmailsService
        {
            static $emails_service_instance = false;

            if( $refresh )
                $emails_service_instance = false;

            if( !$emails_service_instance )
                $emails_service_instance = new \App\Services\System\EmailsService();

            return $emails_service_instance;
        }

    }

    if( helperFunctionCheck('events') ){

        /**
        * @param bool $refresh
        * @return \App\Services\System\EventsService
        */
        function events( bool $refresh = false ) : \App\Services\System\EventsService
        {
            static $events_service_instance = false;

            if( $refresh )
                $events_service_instance = false;

            if( !$events_service_instance )
                $events_service_instance = new \App\Services\System\EventsService();

            return $events_service_instance;
        }

    }

    if( helperFunctionCheck('fileUploads') ){

        /**
        * @param bool $refresh
        * @return \App\Services\System\FileUploadsService
        */
        function fileUploads( bool $refresh = false ) : \App\Services\System\FileUploadsService
        {
            static $file_uploads_service_instance = false;

            if( $refresh )
                $file_uploads_service_instance = false;

            if( !$file_uploads_service_instance )
                $file_uploads_service_instance = new \App\Services\System\FileUploadsService();

            return $file_uploads_service_instance;
        }

    }

    if( helperFunctionCheck('geo') ){

        /**
        * @param bool $refresh
        * @return \App\Services\System\GeoService
        */
        function geo( bool $refresh = false ) : \App\Services\System\GeoService
        {
            static $geo_service_instance = false;

            if( $refresh )
                $geo_service_instance = false;

            if( !$geo_service_instance )
                $geo_service_instance = new \App\Services\System\GeoService();

            return $geo_service_instance;
        }

    }

    if( helperFunctionCheck('localization') ){

        /**
        * @param bool $refresh
        * @return \App\Services\System\LocalizationService
        */
        function localization( bool $refresh = false ) : \App\Services\System\LocalizationService
        {
            static $localization_service_instance = false;

            if( $refresh )
                $localization_service_instance = false;

            if( !$localization_service_instance )
                $localization_service_instance = new \App\Services\System\LocalizationService();

            return $localization_service_instance;
        }

    }

    if( helperFunctionCheck('navigation') ){

        /**
        * @param bool $refresh
        * @return \App\Services\System\NavigationService
        */
        function navigation( bool $refresh = false ) : \App\Services\System\NavigationService
        {
            static $navigation_service_instance = false;

            if( $refresh )
                $navigation_service_instance = false;

            if( !$navigation_service_instance )
                $navigation_service_instance = new \App\Services\System\NavigationService();

            return $navigation_service_instance;
        }

    }

    if( helperFunctionCheck('payments') ){

        /**
        * @param bool $refresh
        * @return \App\Services\System\PaymentsService
        */
        function payments( bool $refresh = false ) : \App\Services\System\PaymentsService
        {
            static $payments_service_instance = false;

            if( $refresh )
                $payments_service_instance = false;

            if( !$payments_service_instance )
                $payments_service_instance = new \App\Services\System\PaymentsService();

            return $payments_service_instance;
        }

    }

    if( helperFunctionCheck('roles') ){

        /**
        * @param bool $refresh
        * @return \App\Services\System\RolesService
        */
        function roles( bool $refresh = false ) : \App\Services\System\RolesService
        {
            static $roles_service_instance = false;

            if( $refresh )
                $roles_service_instance = false;

            if( !$roles_service_instance )
                $roles_service_instance = new \App\Services\System\RolesService();

            return $roles_service_instance;
        }

    }

    if( helperFunctionCheck('settings') ){

        /**
        * @param bool $refresh
        * @return \App\Services\System\SettingsService
        */
        function settings( bool $with_check_access = true, bool $refresh = false ) : \App\Services\System\SettingsService
        {
            static $settings_service_instance = false;

            if( $refresh )
                $settings_service_instance = false;

            if( !$settings_service_instance )
                $settings_service_instance = new \App\Services\System\SettingsService($with_check_access);

            return $settings_service_instance;
        }

    }

    if( helperFunctionCheck('devTools') ){

        /**
        * @param bool $refresh
        * @return \App\Extra\DevTools\Services\DevToolsService
        */
        function devTools( bool $refresh = false ) : \App\Extra\DevTools\Services\DevToolsService
        {
            static $dev_tools_service_instance = false;

            if( $refresh )
                $dev_tools_service_instance = false;

            if( !$dev_tools_service_instance )
                $dev_tools_service_instance = new \App\Extra\DevTools\Services\DevToolsService();

            return $dev_tools_service_instance;
        }

    }

    if( helperFunctionCheck('revered') ){

        /**
        * @param bool $refresh
        * @return \App\Extra\DevTools\Services\ReveredService
        */
        function revered( bool $refresh = false ) : \App\Extra\DevTools\Services\ReveredService
        {
            static $revered_service_instance = false;

            if( $refresh )
                $revered_service_instance = false;

            if( !$revered_service_instance )
                $revered_service_instance = new \App\Extra\DevTools\Services\ReveredService();

            return $revered_service_instance;
        }

    }
