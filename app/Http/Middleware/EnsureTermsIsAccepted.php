<?php

namespace App\Http\Middleware;

use Closure;

class EnsureTermsIsAccepted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    protected $except_urls = [
        'termos-de-uso',
        'logout'
    ];

    public function handle($request, Closure $next)
    {
        $regex = '#' . implode('|', $this->except_urls) . '#';

        if ($request->user() && !$request->user()->fl_accept_terms && !preg_match($regex, $request->path())) {
            return redirect()->route('frontend.termos-de-uso', 1);
        }

        return $next($request);
    }
}
