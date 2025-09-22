<?php

namespace App\Extra\Helpers;


use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class HelperClass
{
    /**
     * Отправить уведомление пользователю (использует EventsService)
     */
    public function notifyUser( \App\Models\User $user, string $title, string $text, string $url = null, string $icon = 'fas fa-info-circle', string $type = 'info' ): void
        {
            // Сохраняем в таблицу
            \App\Models\System\Notification::create([
                'user_id' => $user->id,
                'title'   => $title,
                'text'    => $text,
                'url'     => $url,
                'icon'    => $icon,
                'type'    => $type,
                'created_at' => now(),
            ]);

            // Сбрасываем кеш ленты уведомлений пользователя
            \Illuminate\Support\Facades\Cache::forget('user:'.$user->id.':notifications:last20');
            \Illuminate\Support\Facades\Cache::forget('user:'.$user->id.':notifications:unread_count');

            // И пушим событие для live-обновления
            events()->setClientEvent('notification.add', [
                'title' => $title,
                'text'  => $text,
                'url'   => $url,
                'icon'  => $icon,
                'type'  => $type,
            ], access_key: 'essence_browse', unique: false, for_user: $user);
        }


    /**
     * Получить количество непрочитанных уведомлений пользователя (с кешем)
     */
    public function getUserUnreadNotificationsCount( ?\App\Models\User $user = null ): int
    {
        try {
            $userId = $user?->id ?? (\Illuminate\Support\Facades\Auth::id() ?: null);
            if (!$userId) return 0;

            $cacheKey = 'user:'.$userId.':notifications:unread_count';
            $count = \Illuminate\Support\Facades\Cache::remember($cacheKey, 60, function () use ($userId) {
                return (int) \App\Models\System\Notification::where('user_id', $userId)
                    ->whereNull('read_at')
                    ->count();
            });
            return (int) $count;
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
    *   Вывести контент (локализация)
    */
    public function lang( $key = null,  $replace = [],  $locale = null ) : string|null
    {

        if ( is_null($key) )
            return $key;

        $text = __('content.'.$key, $replace, $locale );

        if( $text == 'content.'.$key )
            $text = __('content.'.$key, $replace, 'ru' );


        if( $text == 'content.'.$key )
            $text = __($key, $replace, $locale);


        //Если режим редактирования локализации
        if( session('localization_edit_mode') && !request()->ajax() ){

            $text = '{{local='.$key.'}}'.$text.'{{/local}}';

        }

        return $text;
    }




    /**
    *   Вывести текст в зависимости от текущей локализации (или в нужной локализации)
    *   Передается массив, где ключ это код языка, значение - текст на нужном языке.
    *   Если значение не найдено - сперва пытаемся вывести на том языке, который доступен (язык по умолчанию), если не найдено - выводим null.
    */
    public function langText( $text,  $lang = false ) : string|null
    {

        if( $lang == false )
            $lang = app()->getLocale();

        if( isset($text[$lang]) && $text[$lang] !== 'NULL' && trim(strip_tags($text[$lang])) != 'NULL' )
            return $text[$lang];

        $lang = array_key_first( config('settings.languages') );
        if( isset($text[$lang]) )
            return $text[$lang];

        if( is_array($text) ) {

            $lang = array_key_first($text);
            if (isset($text[$lang]))
                return $text[$lang];

        }


        if( is_array($text) )
            return null;


        return $text;

    }



    /**
    *   Вывести текст в зависимости от текущей локализации (или в нужной локализации)
    *   Передается массив, где ключ это код языка, значение - текст на нужном языке.
    *   Если значение не найдено - выводим null.
    */
    public function langTextOrNull( $text,  $lang = false ) : string|null
    {

        if( $lang == false )
            $lang = app()->getLocale();

        if( isset($text[$lang]) )
            return $text[$lang];

        return null;

    }




    /**
     * Получить список Routes [name => path]
     */
    public function getRoutesListByName( $toJson = true ): array|string
    {

        $routes = [];

        foreach( Route::getRoutes()->getRoutesByName() as $route_name => $route )
            $routes[$route_name] = $route->uri;

        if( $toJson )
            $routes = json_encode( $routes );

        return $routes;

    }



    /**
     * Получить ключ шаблона для error страницы
     */
    public function getKeyTemplateForErrorPage(): string
    {

        return admin()->checkCanSeeAndRouteOpen() ? 'admin.components.error-layout' : 'errors::illustrated-layout';

    }



    /**
     * Если первый аргумент true, то возвращает второй, иначе - третий
     */
    public function r( $if, $true_val, $false_val = '' ){

        if( $if )
            return $true_val;

        return $false_val;

    }


    /**
     * Код выполняется на Prod или нет
     */
    public function isProduction(): bool
    {

        return app()->isProduction();

    }


    /**
     * Включен ли Debug сейчас или нет
     */
    public function isDebug(): bool
    {

        return env('APP_DEBUG', true);

    }



    /**
     * Пагинация для array|collection items
     */
    public function paginateItems( $items, $perPage = 20, $page = null, $options = [] ): LengthAwarePaginator
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        if( !isset($options['path']) )
            $options['path'] = request()->url();

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }


    /**
     * Выбросить исключение
     */
    public function error( string $msg, int $code = 422 ): never
    {

        abort($code, $msg);

    }


    /**
     * Максимальный размер файла, который можно на сервер загрузить
     */
    public function getPostMaxSize( $as_bytes = false ): string|int
    {
        if( is_numeric($postMaxSize = ini_get('post_max_size')) ){

            $bytes = (int) $postMaxSize;

        }else{

            $metric = strtoupper(substr($postMaxSize, -1));
            $postMaxSize = (int) $postMaxSize;

            $bytes = match ($metric) {
                'K' => $postMaxSize * 1024,
                'M' => $postMaxSize * 1048576,
                'G' => $postMaxSize * 1073741824,
                default => $postMaxSize,
            };

        }

        if( $as_bytes )
            return $bytes;

        return ($bytes / 1048576).' МБ';

    }


    /**
     * Проверяет, истекло ли время для подтверждения пароля (для скрытых зон)
     *
     */
    public function checkConfirmPassword( bool $with_redirect = true, $passwordTimeoutSeconds = null ): RedirectResponse|bool
    {
        $confirmedAt = time() - request()->session()->get('auth.password_confirmed_at', 0);

        $should_confirm_pass = $confirmedAt > ($passwordTimeoutSeconds ?? config('auth.password_timeout'));

        if( $should_confirm_pass && $with_redirect ) {

            $section = admin()->isRouteOpen() ? 'admin' : 'guest';

            $url = route('password.confirm', ['backTo' => urlencode(request()->getRequestUri()), 'section' => $section]);

            if( request()->wantsJson() ){

                $this->jsonRedirectResponse($url);

            }else{

                redirect()->to($url)->throwResponse();

            }

        }

        return $should_confirm_pass;

    }


    /**
     * Json Redirect Response
     */
    public function jsonRedirectResponse( string $redirect_url ): void
    {

       response()->json([
           'redirectTo' => $redirect_url
       ])->throwResponse();

    }



    /** - Singleton - */
    protected static ?HelperClass $_instance = null;
    private function __construct() {}
    public static function getInstance(): HelperClass
    {
        if( self::$_instance === null ){
            self::$_instance = new self;
        }
        return self::$_instance;
    }
    public function __clone() {}
    public function __wakeup() {}


}
