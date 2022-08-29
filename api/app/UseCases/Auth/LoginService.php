<?php

namespace App\UseCases\Auth;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;

class LoginService
{
    private Client $client;

    public function __construct() {
        $this->client = new Client([
            'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
            'http_errors' => false,
            'verify' => false
        ]);
    }

    public function phoneAuth(string $phone, string $password): void
    {
        $url = config('data.loyalty.test.url.lk') . '/Identity/AdvancedPhoneEmailLogin';
        $partnerId = config('data.loyalty.test.partner_id');
        $data = [
            'PhoneOrEmail' => $phone,
            'Password' => $password,
            'PartnerId' => $partnerId
        ];

        $response = $this->client->post($url, ['body' => json_encode(['parameter' => $data])]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        if (!$user = User::query()->find($data['Id']))
            throw new \DomainException('Пользователь не найден');

        $user->update(['session' => $data['SessionId']]);
        Auth::login($user);
    }

    public function loginAuth(string $login, string $password): void
    {
        $url = config('data.loyalty.test.url.lk') . '/Identity/Login';
        $data = ['Login' => $login, 'Password' => $password];

        $response = $this->client->post($url, ['body' => json_encode(['parameter' => $data])]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        if (!$user = User::query()->find($data['Id']))
            throw new \DomainException('Пользователя не найден');

        $user->update(['session' => $data['SessionId']]);
        Auth::login($user);
    }
}
