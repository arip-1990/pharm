<?php

namespace App\UseCases\User;

use App\Http\Requests\User\UpdatePasswordRequest;
use App\Models\User;
use App\UseCases\LoyaltyService;
use App\UseCases\ManagerService;
use Carbon\Carbon;

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
        $entity = [
            'Id' => $user->id,
            'MobilePhone' => $user->phone,
            'EmailAddress' => $user->email,
            'Firstname' => $user->first_name,
            'Lastname' => $user->last_name,
            'MiddleName' => $user->middle_name,
            'BirthDate' => $user->birth_date?->format('Y-m-d'),
            'GenderCode' => $user->gender,
            'MobilePhoneVerified' => true,
            'AllowNotification' => false,
            'AllowEmail' => false,
            'AllowSms' => false,
            'AgreeToTerms' => false,
            'PartnerId' => $this->config['partner_id']
        ];

        if ($newData) {
            $entity['EmailAddress'] = $newData['email'] ?? $user->email;
            $entity['Firstname'] = $newData['firstName'] ?? $user->first_name;
            $entity['Lastname'] = $newData['lastName'];
            $entity['MiddleName'] = $newData['middleName'];
            $entity['BirthDate'] = $newData['birthDate']?->format('Y-m-d') ?? $user->birth_date?->format('Y-m-d');
            $entity['GenderCode'] = $newData['gender'] ?? $user->gender;
        }

        $data = [
            'Entity' => $entity,
            'SessionId' => $session ?? $this->managerService->login()['sessionId']
        ];

        $response = $this->client->post($url, ['json' => ['parameter' => json_encode($data)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        $user->update([
            'first_name' => $entity['Firstname'],
            'last_name' => $entity['Lastname'],
            'middle_name' => $entity['MiddleName'],
            'email' => $entity['EmailAddress'],
            'birth_date' => Carbon::parse($entity['BirthDate']),
            'gender' => $entity['GenderCode']
        ]);

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
