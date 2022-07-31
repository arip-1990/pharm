<?php

namespace App\UseCases\Auth;

use App\Http\Requests\Auth\LoginRequest;
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

    public function phoneAuth(LoginRequest $request): void
    {
        $url = config('data.loyalty.test.url.lk') . '/Identity/AdvancedPhoneEmailLogin';
        $partnerId = config('data.loyalty.test.partner_id');
        $data = [
            'PhoneOrEmail' => $request->get('login'),
            'Password' => $request->get('password'),
            'PartnerId' => $partnerId
        ];

        $response = $this->client->post($url, ['body' => json_encode(['parameter' => $data])]);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($response->getBody()->getContents());

        $data = json_decode($response->getBody());
        if (!$user = User::query()->find($data['Id']))
            throw new \DomainException('Пользователя не найден');

        $user->update(['session' => $data['SessionId']]);
        Auth::login($user);
        $request->session()->regenerate();
    }

    public function loginAuth(LoginRequest $request): void
    {
        $url = config('data.loyalty.test.url.lk') . '/Identity/Login';
        $partnerId = config('data.loyalty.test.partner_id');
        $data = [
            'Login' => $request->get('login'),
            'Password' => $request->get('password'),
            'PartnerId' => $partnerId
        ];

        $response = $this->client->post($url, ['body' => json_encode(['parameter' => $data])]);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($response->getBody()->getContents());

        $data = json_decode($response->getBody());
        if (!$user = User::query()->find($data['Id']))
            throw new \DomainException('Пользователя не найден');

        $user->update(['session' => $data['SessionId']]);
        Auth::login($user);
        $request->session()->regenerate();
    }
}
