<?php

namespace App\Services\DaData;

class DaDataClient
{
    private CleanClient $cleaner;
    private ProfileClient $profile;
    private SuggestClient $suggestions;

    public function __construct(string $token, string $secret)
    {
        $this->cleaner = new CleanClient($token, $secret);
        $this->profile = new ProfileClient($token, $secret);
        $this->suggestions = new SuggestClient($token, $secret);
    }

    public function clean($name, $value)
    {
        return $this->cleaner->clean($name, $value);
    }

    public function cleanRecord($structure, $record)
    {
        return $this->cleaner->cleanRecord($structure, $record);
    }

    public function findAffiliated($query, $count = null, $kwargs = [])
    {
        return $this->suggestions->findAffiliated($query, $count ?? config('dadata.suggestion_count'), $kwargs);
    }

    public function findById($name, $query, $count = null, $kwargs = [])
    {
        return $this->suggestions->findById($name, $query, $count ?? config('dadata.suggestion_count'), $kwargs);
    }

    public function geoLocate($name, $lat, $lon, $radiusMeters = 100, $count = null, $kwargs = [])
    {
        return $this->suggestions->geoLocate($name, $lat, $lon, $radiusMeters, $count ?? config('dadata.suggestion_count'), $kwargs);
    }

    public function getBalance()
    {
        return $this->profile->getBalance();
    }

    public function getDailyStats($date = null)
    {
        return $this->profile->getDailyStats($date);
    }

    public function getVersions()
    {
        return $this->profile->getVersions();
    }

    public function ipLocate($ip, $kwargs = [])
    {
        return $this->suggestions->ipLocate($ip, $kwargs);
    }

    public function suggest($name, $query, $count = null, $kwargs = [])
    {
        return $this->suggestions->suggest($name, $query, $count ?? config('dadata.suggestion_count'), $kwargs);
    }
}
