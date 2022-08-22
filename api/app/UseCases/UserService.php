<?php

namespace App\UseCases;

use App\Models\User;
use GuzzleHttp\Client;

class UserService
{
    private Client $client;

    public function __construct() {
        $this->client = new Client([
            'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
            'http_errors' => false,
            'verify' => false
        ]);
    }

    public function userInfo(User $user): array
    {
        $url = config('data.loyalty.test.url.lk') . '/Contact/Get';
        $data = [
            'id' => $user->id,
            'sessionid' => $user->session,
        ];

        $response = $this->client->get($url, ['query' => $data]);
        if ($response->getStatusCode() !== 200)
            throw new \DomainException($response->getBody()->getContents());

        return json_decode($response->getBody(), true);
    }

    public function userUpdate(User $user, array $newData = []): string
    {
        $url = config('data.loyalty.test.url.lk') . '/Contact/Update';
        $partnerId = config('data.loyalty.test.partner_id');
        $data = [
            'MobilePhone' => '+' . ($newData['phone'] ?? $user->phone),
            'EmailAddress' => $newData['email'] ?? $user->email,
            'Firstname' => $newData['first_name'] ?? $user->first_name,
            'Lastname' => $newData['last_name'] ?? $user->last_name,
            'MiddleName' => $newData['middle_name'] ?? $user->middle_name,
            'BirthDate' => $newData['birth_date'] ?? $user->birth_date,
            'GenderCode' => $newData['gender'] ?? $user->gender,
            'AllowNotification' => false,
            'AllowEmail' => false,
            'AllowSms' => false,
            'AgreeToTerms' => true,
            'PartnerId' => $partnerId
        ];

        $response = $this->client->post($url, ['body' => json_encode(['parameter' => ['Entity' => $data]])]);
        if ($response->getStatusCode() !== 200)
            throw new \DomainException($response->getBody()->getContents());

        return json_decode($response->getBody(), true)['value'];
    }

    public function setPassword(string $userId, string $password): void
    {
        $url = config('data.loyalty.test.url.admin') . '/User/CreatePassword';
        $sessionId = config('data.loyalty.test.session_id');
        $data = [
            'SessionId' => $sessionId,
            'Id' => $userId,
            'Password' => $password,
        ];

        $response = $this->client->post($url, ['body' => json_encode(['parameter' => $data])]);
        if ($response->getStatusCode() !== 200)
            throw new \DomainException($response->getBody()->getContents());
    }
}
