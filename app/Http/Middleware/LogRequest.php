<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

/**
 * Class Authenticate.
 */
class LogRequest extends Middleware
{
    public function handle($request, $next, ...$guards)
    {
        /** Tratamento específico p/ não logar as senhas no arquivo de logs do sistema  */
        $all = $request->all();
        if (isset($all['password'])) {
            unset($all['password']);
        }

        if (isset($all['password_confirmation'])) {
            unset($all['password_confirmation']);
        }

        /** Tratamento específico p/ não logar a key de acesso aos dados (DadoModel)  */
        if (isset($all['api_token'])) {
            unset($all['api_token']);
        }

        info($request->fullUrl(), $all);
        return $next($request);
    }
}
