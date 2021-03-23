<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

/**
 * Class PermissionReportRestrict.
 */
class PermissionReportRestrict
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user() && $request->user()->can('report restricted')) {
            abort(Response::HTTP_UNAUTHORIZED);
            return;
        }

        return $next($request);
    }
}
