<?php

namespace App\Http\Middleware;

use Dadata\DadataClient;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\AbstractDeviceParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Closure;

class Statistic
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->header('cf-connecting-ip', $request->ip());
        $userAgent = $request->header('user-agent');
        AbstractDeviceParser::setVersionTruncation(AbstractDeviceParser::VERSION_TRUNCATION_NONE);
        $dd = new DeviceDetector($userAgent);
        $dd->parse();

        if (!$dd->isBot()) {
            $dadata = new DadataClient(config('data.dadata_api.token'), config('data.dadata_api.secret'));
            if ($request->session()->has('static_id')) {
                /** @var \App\Entities\Statistic $statistic */
                $statistic = \App\Entities\Statistic::query()->find($request->session()->get('static_id'));
                if (!$statistic->city) {
                    $response = $dadata->iplocate($ip);
                    $statistic->city = $response['unrestricted_value'];
                }
                $statistic->updated_at = new \DateTimeImmutable();
                if (!$statistic->user and Auth::user())
                    $statistic->user()->associate(Auth::user());
                $statistic->save();
            } elseif ($request->session()->has('static_created')) {
                $created = $request->session()->get('static_created');
                if ($created->diff(new \DateTimeImmutable())->format('s')) {
                    $os = $dd->getOs();
                    $os = $os['name'] . ' ' . $os['platform'];
                    $browser = $dd->getClient();
                    $browser = $browser['name'] . ' ' . $browser['version'];
                    $response = $dadata->iplocate($ip);
                    $city = $response['unrestricted_value'];
                    $statistic = \App\Entities\Statistic::create($ip, $os, $browser, city: $city, referrer: $request->header('referer'));
                    $statistic->created_at = $created;
                    if (Auth::user())
                        $statistic->user()->associate(Auth::user());
                    $statistic->save();
                    $request->session()->now('static_id', $statistic->id);
                }
            } else {
                $request->session()->now('static_created', new \DateTimeImmutable());
            }
        }

        return $next($request);
    }
}
