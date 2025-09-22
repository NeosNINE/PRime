<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Locale
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle( Request $request, Closure $next): Response
    {

        if( session()->has('locale') ){

            app()->setLocale( session('locale') );

        }else{

            $locale = 'ru';

            session(['locale' => $locale]);
            app()->setLocale( $locale );
        }

        return $next($request);
    }
}
