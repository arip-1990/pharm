<?php

namespace App\Services\DaData;

use Carbon\Carbon;

class ProfileClient extends ClientBase
{
    public function __construct(string $token, string $secret)
    {
        parent::__construct(config('dadata.profile_url'), $token, $secret);
    }

    public function getBalance()
    {
        $url = "profile/balance";
        $response = $this->get($url);

        return $response["balance"];
    }

    public function getDailyStats(Carbon $date = null)
    {
        $url = "stat/daily";
        if (!$date) $date = Carbon::now();

        $query = ["date" => $date->format("Y-m-d")];
        $response = $this->get($url, $query);

        return $response;
    }

    public function getVersions()
    {
        $url = "version";
        $response = $this->get($url);

        return $response;
    }
}
