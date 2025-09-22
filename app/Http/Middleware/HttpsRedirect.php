<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HttpsRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle( Request $request, Closure $next ): Response
    {

        if( !$request->secure() && str(env('APP_URL'))->startsWith('https') )
            return redirect()->secure($request->getRequestUri());

        return $next($request);
    }
}
