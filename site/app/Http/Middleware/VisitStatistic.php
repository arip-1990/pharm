<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;

class VisitStatistic
{
    public function handle(Request $request, \Closure $next)
    {
        $ip = $request->get('cf-connecting-ip', $request->ip());
        if (config('app.env') === 'production' and !in_array($ip, ['78.142.233.153', '109.70.189.119'])) {
            (new \App\Services\VisitStatistic($request))->handle();
        }

        return $next($request);
    }
}
