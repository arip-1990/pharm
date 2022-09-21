<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;

class AuthenticateOnceWithBasicAuth
{
    public function handle($request, $next)
    {
        return Auth::guard('web')->onceBasic('username') ?: $next($request);
    }
}
