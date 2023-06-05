<?php

namespace App\Services\DaData;

use GuzzleHttp\Client;

abstract class ClientBase
{
    public Client $client;

    public function __construct(string $baseUrl, string $token, string $secret = null)
    {
        $headers = [
            "Content-Type" => "application/json",
            "Accept" => "application/json",
            "Authorization" => "Token " . $token,
        ];
        if ($secret) $headers["X-Secret"] = $secret;

        $this->client = new Client([
            "base_uri" => $baseUrl,
            "headers" => $headers,
            "timeout" => config('dadata.timeout_sec')
        ]);
    }

    protected function get($url, $query = [])
    {
        $response = $this->client->get($url, ["query" => $query]);

        return json_decode($response->getBody(), true);
    }

    protected function post($url, $data)
    {
        $response = $this->client->post($url, ["json" => $data]);

        return json_decode($response->getBody(), true);
    }
}
