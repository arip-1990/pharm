<?php

namespace App\UseCases\Auth;

use GuzzleHttp\Client;

class PasswordService
{
    private Client $client;

    public function __construct() {
        $this->client = new Client([
            'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
            'http_errors' => false,
            'verify' => false
        ]);
    }

    public function requestResetPassword(string $phone): string
    {
        $url = config('data.loyalty.test.url.lk') . '/User/RequestPasswordChange';
        $partnerId = config('data.loyalty.test.partner_id');
        $data = [
            'PhoneOrEmail' => $phone,
            'PartnerId' => $partnerId
        ];

        $response = $this->client->post($url, ['json' => ['parameter' => json_encode($data)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        return $data['value'];
    }

    public function validateTempPassword(string $token, string $password): string
    {
        $url = config('data.loyalty.test.url.lk') . '/User/ValidateTempPassword';
        $data = [
            'Token' => $token,
            'TemporaryPassword' => $password
        ];

        $response = $this->client->post($url, ['json' => ['parameter' => json_encode($data)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        return $data['value'];
    }

    public function changePassword(string $token, string $password): void
    {
        $url = config('data.loyalty.test.url.lk') . '/User/ChangePassword';
        $data = [
            'Token' => $token,
            'Password' => $password
        ];

        $response = $this->client->post($url, ['json' => ['parameter' => json_encode($data)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);
    }

}
