<?php

namespace App\UseCases\Auth;

use App\Http\Requests\Auth\VerifyPhoneRequest;
use GuzzleHttp\Client;

class VerifyService
{
    private Client $client;

    public function __construct() {
        $this->client = new Client([
            'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
            'http_errors' => false,
            'verify' => false
        ]);
    }

    public function handle(VerifyPhoneRequest $request): string
    {
        $url = config('data.loyalty.test.url.lk') . '/Identity';
        $partnerId = config('data.loyalty.test.partner_id');
        $token = $request->session()->get('token');

        $data = [
            'Token' => $token,
            'Code' => $request->get('code'),
            'PartnerId' => $partnerId
        ];

        $response = $this->client->post($url . '/CheckSmsForRegistration', ['body' => json_encode(['parameter' => $data])]);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($response->getBody()->getContents());

        return json_decode($response->getBody())['Token'];
    }
}
