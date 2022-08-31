<?php

namespace App\UseCases;

use GuzzleHttp\Client;
use JetBrains\PhpStorm\ArrayShape;

class ManagerService
{
    private Client $client;

    public function __construct() {
        $this->client = new Client([
            'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
            'http_errors' => false,
            'verify' => false
        ]);
    }

    #[ArrayShape(['id' => "string", 'sessionId' => "string"])]
    public function login(): array
    {
        $url = config('data.loyalty.test.url.manager') . '/Identity/Login';
        $manager = config('data.loyalty.test.manager');
        $data = ['Login' => $manager['login'], 'Password' => $manager['password']];

        $response = $this->client->post($url, ['json' => ['parameter' => json_encode($data)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        return ['id' => $data['Id'], 'sessionId' => $data['SessionId']];
    }
}
