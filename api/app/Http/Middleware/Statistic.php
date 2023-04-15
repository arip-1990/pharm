<?php

namespace App\Http\Middleware;

use App\Models\VisitStatistic;
use App\Services\IpInfo\IpInfo;
use Closure;
use hisorange\BrowserDetect\Parser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class Statistic
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $ip = $this->getIP($request);
            if (Parser::isBot() or $ip === '78.142.233.153' or $ip === '89.151.178.52')
                throw new \Exception();

            $ipInfo = new IpInfo();
            if ($visitId = Session::get('visitId')) {
                $visit = VisitStatistic::find($visitId);
                if (!$visit->city) {
                    $details = $ipInfo->getDetails($ip);
                    $visit->city = "{$details->postal}, {$details->city}";
                }

                if (!$visit->user and $user = $request->user())
                    $visit->user()->associate($user);

                $visit->save();
            }
            else {
                $details = $ipInfo->getDetails($ip);
                $visit = new VisitStatistic([
                    'ip' => $ip,
                    'os' => Parser::platformName(),
                    'browser' => Parser::browserName(),
                    'city' => "{$details->postal}, {$details->city}",
                    'referrer' => $request->header('referer')
                ]);

                if ($user = $request->user()) $visit->user()->associate($user);

                $visit->save();
                Session::put('visitId', $visit->id);
            }
        } catch (\Exception $e) {}

        return $next($request);
    }

    public function getIp(Request $request): string
    {
        if ($xForwardedFor = $request->header('x-forwarded-for')) {
            $ips = explode(',', $xForwardedFor);
            $ip = trim($ips[0]);
        }
        else $ip = $request->header('cf-connecting-ip', $request->ip());

        return $ip;
    }
}
