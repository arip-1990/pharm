<?php

namespace App\Http\Middleware;

use App\Models\VisitStatistic;
use App\Services\DaData\DaDataClient;
use hisorange\BrowserDetect\Parser;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Statistic
{
    public function handle(Request $request, \Closure $next): Response
    {
        try {
            $ip = $this->getIP($request);
            $detect = (new Parser(null, $request))->detect();

            if ($detect->isBot() or $ip === '78.142.233.153' or $ip === '89.151.178.52')
                throw new \DomainException();

            $daData = new DaDataClient(config('dadata.DADATA_TOKEN'), config('dadata.DADATA_SECRET'));
            if ($visitId = $request->session()->get('visitId')) {
                $visit = VisitStatistic::find($visitId);
                if (!$visit->city) {
                    $details = $daData->ipLocate($ip);
                    $visit->city = "{$details['data']['postal_code']}, {$details['data']['city']}";
                }

                if (!$visit->user and $user = $request->user())
                    $visit->user()->associate($user);

                $visit->touch();
            }
            else {
                $details = $daData->ipLocate($ip);
                $visit = new VisitStatistic([
                    'ip' => $ip,
                    'os' => $detect->platformName(),
                    'browser' => $detect->browserName(),
                    'city' => "{$details['data']['postal_code']}, {$details['data']['city']}",
                    'referrer' => $request->header('referer')
                ]);

                if ($user = $request->user()) $visit->user()->associate($user);

                $visit->save();
                $request->session()->put('visitId', $visit->id);
            }
        }
        catch (\Exception $e) {}
        finally {
            return $next($request);
        }
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
