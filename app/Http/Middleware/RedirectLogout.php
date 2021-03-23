<?php

namespace App\Http\Middleware;

use Closure;

class RedirectLogout
{

    public function handle($request, Closure $next, $guard = null)
    {
        @auth()->logout();

        return $next($request);
    }
}
