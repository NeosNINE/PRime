<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserLastActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle( Request $request, Closure $next ): Response
    {

        if( !Auth::check() || users()->isImpersonating() )
            return $next($request);

        $user = Auth::user();

        //Обновляем время последней активности только если оно NULL или обновлялось более минуты назад
        if( is_null($user->last_active_at) || $user->last_active_at < Carbon::now()->subMinute() ){
            $user->last_active_at = Carbon::now();
            $user->save();
        }

        return $next($request);
    }
}
