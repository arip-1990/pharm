<?php

namespace App\Services\DaData;

class SuggestClient extends ClientBase
{
    public function __construct($token, $secret = null)
    {
        parent::__construct(config('dadata.suggestions_url'), $token, $secret);
    }

    public function findAffiliated($query, $count = null, $kwargs = [])
    {
        $url = "findAffiliated/party";
        $data = ["query" => $query, "count" => $count ?? config('dadata.suggestion_count')];
        $data = $data + $kwargs;
        $response = $this->post($url, $data);

        return $response["suggestions"];
    }

    public function findById($name, $query, $count = null, $kwargs = [])
    {
        $url = "findById/$name";
        $data = ["query" => $query, "count" => $count ?? config('dadata.suggestion_count')];
        $data = $data + $kwargs;
        $response = $this->post($url, $data);

        return $response["suggestions"];
    }

    public function geoLocate($name, $lat, $lon, $radiusMeters = 100, $count = null, $kwargs = [])
    {
        $url = "geolocate/$name";
        $data = array(
            "lat" => $lat,
            "lon" => $lon,
            "radius_meters" => $radiusMeters,
            "count" => $count ?? config('dadata.suggestion_count'),
        );
        $data = $data + $kwargs;
        $response = $this->post($url, $data);
        return $response["suggestions"];
    }

    public function ipLocate($ip, $kwargs = [])
    {
        $url = "iplocate/address";
        $query = ["ip" => $ip];
        $query = $query + $kwargs;
        $response = $this->get($url, $query);

        return $response["location"];
    }

    public function suggest($name, $query, $count = null, $kwargs = [])
    {
        $url = "suggest/$name";
        $data = ["query" => $query, "count" => $count ?? config('dadata.suggestion_count')];
        $data = $data + $kwargs;
        $response = $this->post($url, $data);

        return $response["suggestions"];
    }
}
