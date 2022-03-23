<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;

class VisitStatistic
{
    public function handle(Request $request, \Closure $next)
    {
        if (config('app.env') === 'production' and $request->getSchemeAndHttpHost() === env('APP_URL') and $request->ip() !== '78.142.233.153') {
            (new \App\Services\VisitStatistic($request))->handle();
        }

        return $next($request);
    }
}
