<?php

namespace App\UseCases\User;

use App\Http\Requests\User\UpdatePasswordRequest;
use App\Models\User;
use App\UseCases\LoyaltyService;
use App\UseCases\ManagerService;

class UserService extends LoyaltyService
{
    public function __construct(private readonly ManagerService $managerService) {
        parent::__construct();
    }

    public function getInfo(string $id, string $session = null): array
    {
        $url = $this->urls['lk'] . '/Contact/Get';
        $session = $session ?? $this->managerService->login()['sessionId'];

        $response = $this->client->get($url, ['query' => "id='{$id}'&sessionid='{$session}'"]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        return $data;
    }

    public function updateInfo(User $user, array $newData = [], string $session = null): string
    {
        $url = $this->urls['lk'] . '/Contact/Update';

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
                'PartnerId' => $this->config['partner_id']
            ],
            'SessionId' => $session ?? $this->managerService->login()['sessionId']
        ];

        $response = $this->client->post($url, ['json' => ['parameter' => json_encode($data)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        return $data['value'];
    }

    public function updatePassword(User $user, UpdatePasswordRequest $request): void
    {
        $url = $this->urls['lk'] . '/Identity/UpdatePassword';
        $data = [
            'id' => $user->id,
            'sessionid' => $user->session,
            'oldpassword' => $request->get('oldPassword'),
            'password' => $request->get('password'),
        ];

        $response = $this->client->post($url, ['json' => ['parameter' => json_encode($data)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);
    }

    public function setPassword(string $userId, string $password): void
    {
        $url = $this->urls['admin'] . '/User/CreatePassword';
        $data = [
            'Id' => $userId,
            'Password' => $password,
            'SessionId' => $this->config['session_id']
        ];

        $response = $this->client->post($url, ['json' => ['parameter' => json_encode($data)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);
    }
}
