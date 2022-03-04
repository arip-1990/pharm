<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;

use Closure;

class VisitStatistic
{
    public function handle(Request $request, Closure $next)
    {
        (new \App\Services\VisitStatistic($request))->handle();
        return $next($request);
    }
}
