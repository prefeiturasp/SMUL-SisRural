<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Class AuthenticateSessionUpdate.
 */
class AuthenticateSessionUpdate
{
    public function handle($request, Closure $next)
    {
        if (!$request->hasSession() || !$request->user()) {
            return $next($request);
        }

        //Força a sessão caso o usuário esteja autenticado
        if ($request->user() && !$request->session()->get('auth_user_id')) {
            session(['auth_user_id' => $request->user()->id]);
            session(['auth_user_role' => $request->user()->roles->first()->name]);
        }

        return $next($request);
    }
}
