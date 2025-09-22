<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Получить URL для перенаправления после авторизации/регистрации и т.д.
     * @param array $query_params
     * @return string
     */
    public static function getRedirectUrl( array $query_params = [] ): string
    {

        if( request()->input('backTo') )
            return urldecode(request()->input('backTo'));

        if( Auth::check() ){

            if( roles()->checkAccess('*', considerImpersonator: false) ){

                return route('admin.index', $query_params);

            }else{

                return route('user.profile', $query_params);

            }

        }

        return route('index', $query_params);

    }


    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
