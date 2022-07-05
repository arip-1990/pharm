<?php

namespace App\Services;

use Carbon\Carbon;
use Dadata\DadataClient;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\AbstractDeviceParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitStatistic
{
    public function __construct(private Request $request) {}

    public function handle(): void
    {
        $ip = $this->request->header('cf-connecting-ip', $this->request->ip());
        $userAgent = $this->request->header('user-agent');
        AbstractDeviceParser::setVersionTruncation(AbstractDeviceParser::VERSION_TRUNCATION_NONE);
        $dd = new DeviceDetector($userAgent);
        $dd->parse();

        if ($dd->isBot()) return;

        $dadata = new DadataClient(config('data.dadata_api.token'), config('data.dadata_api.secret'));
        /** @var \App\Models\VisitStatistic $statistic */
        if ($statistic = \App\Models\VisitStatistic::query()->find($this->request->session()->get('static_id'))) {
            if (!$statistic->city) {
                $response = $dadata->iplocate($ip);
                $statistic->city = $response['unrestricted_value'] ?? null;
            }
            if (!$statistic->user and Auth::check())
                $statistic->user()->associate(Auth::user());

            $statistic->update(['updated_at' => Carbon::now()]);
        }
        else {
            $os = $dd->getOs();
            $browser = $dd->getClient();
            $response = $dadata->iplocate($ip);
            $statistic = \App\Models\VisitStatistic::query()->create([
                'ip' => $ip,
                'os' => $os['name'] . ' ' . $os['platform'],
                'browser' => $browser['name'] . ' ' . $browser['version'],
                'city' => $response['unrestricted_value'] ?? null,
                'referer' => $this->request->header('referer')
            ]);
            if (Auth::check())
                $statistic->user()->associate(Auth::user());

            $this->request->session()->put('static_id', $statistic->id);
        }
    }
}
