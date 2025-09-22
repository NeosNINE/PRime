<?php

namespace App\Http\Middleware;

use Barryvdh\Debugbar\Facades\Debugbar;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class DebugForDevsMiddleware
{

    /**
     * Для разработчиков всегда включаем debug
     */
    public function handle( Request $request, Closure $next )
    {

        if( Auth::check() && Auth::user()->isDeveloper() ){

            Config::set('app.debug', true);

            try {

                if( config('env.DEBUGBAR_ENABLED') )
                    Debugbar::enable();

            } catch ( \Throwable $throwable ){

            }

        }

        return $next($request);

    }

}
