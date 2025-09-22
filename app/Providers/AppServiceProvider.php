<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {

        $env = env('APP_ENV', 'production');

        if( $env != 'staging' && $env != 'production' ){

            try {

                // IDE helper (optional in dev). Suppress static analysis warnings without hard type import
                $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);

            } catch ( \Throwable $throwable ){

            }

        }

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {

        // Принудительное использование HTTPS для всех генерируемых ссылок
        if ( str_starts_with(env('APP_URL', ''), 'https://') )
            URL::forceScheme('https');

        $context = [
            'url' => urldecode(request()->url()),
            'method' => request()->method()
        ];

        $data = request()->all();

        if( count($data) )
            $context['params'] = $data;

        Log::withContext($context);

    }
}
