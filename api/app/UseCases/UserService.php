<?php

namespace App\UseCases;

use App\Http\Requests\User\UpdatePasswordRequest;
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

    public function getInfo(User $user): array
    {
        $url = config('data.loyalty.test.url.lk') . '/Contact/Get';
        $data = [
            'id' => $user->id,
            'sessionid' => $user->session,
        ];

        $response = $this->client->get($url, ['query' => $data]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        return $data;
    }

    public function updateInfo(User $user, array $newData = []): string
    {
        $url = config('data.loyalty.test.url.lk') . '/Contact/Update';
        $partnerId = config('data.loyalty.test.partner_id');
        $data = [
            'Entity' => [
                'Id' => $user->id,
                'MobilePhone' => $user->phone,
                'EmailAddress' => $newData['email'] ?? $user->email,
                'Firstname' => $newData['firstName'] ?? $user->first_name,
                'Lastname' => $newData['lastName'] ?? $user->last_name,
                'MiddleName' => $newData['middleName'] ?? $user->middle_name,
                'BirthDate' => $newData['birthDate'] ?? $user->birth_date?->format('Y-m-d'),
                'GenderCode' => $newData['gender'] ?? $user->gender,
                'MobilePhoneVerified' => true,
                'AllowNotification' => false,
                'AllowEmail' => false,
                'AllowSms' => false,
                'AgreeToTerms' => false,
                'PartnerId' => $partnerId
            ],
            'SessionId' => $user->session ?? 'menageSessionId' // TODO replace
        ];

        $response = $this->client->post($url, ['body' => json_encode(['parameter' => $data])]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        return $data['value'];
    }

    public function updatePassword(User $user, UpdatePasswordRequest $request): void
    {
        $url = config('data.loyalty.test.url.lk') . '/Identity/UpdatePassword';
        $data = [
            'id' => $user->id,
            'sessionid' => $user->session,
            'oldpassword' => $request->get('oldPassword'),
            'password' => $request->get('password'),
        ];

        $response = $this->client->post($url, ['body' => json_encode(['parameter' => $data])]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);
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
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);
    }
}
