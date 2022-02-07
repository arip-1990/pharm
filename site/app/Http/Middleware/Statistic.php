<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;

use Closure;

class Statistic
{
    public function handle(Request $request, Closure $next)
    {
        (new \App\Services\Statistic($request))->handle();
        return $next($request);
    }
}
