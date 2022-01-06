<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth as BaseAuthMiddleware;

class AuthenticateWithBasicAuth extends BaseAuthMiddleware
{
    public function handle($request, $next, $guard = null, $field = null)
    {
        $this->auth->guard($guard)->basic($field ?: 'name');

        return $next($request);
    }
}
