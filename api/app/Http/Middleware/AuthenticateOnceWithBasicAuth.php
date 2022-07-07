<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;

class AuthenticateOnceWithBasicAuth
{
    public function handle($request, $next)
    {
        return Auth::onceBasic('name') ?: $next($request);
    }
}
