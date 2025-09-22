<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccessMiddleware
{
    public function handle( Request $request, Closure $next, $role_key ): Response
    {

        roles()->checkAccessWithAbort($role_key);

        return $next($request);
    }
}
