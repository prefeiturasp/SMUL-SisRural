<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

/**
 * Class Authenticate.
 */
class AppSync extends Middleware
{
    public function handle($request, $next, ...$guards)
    {
        \Config::set('app_sync', true);

        return $next($request);
    }
}
