<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;

class AuthenticateOnceWithBasicAuth
{
    public function handle($request, $next)
    {
        return Auth::onceBasic('first_name') ?: $next($request);
    }
}
