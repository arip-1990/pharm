<?php

namespace App\UseCases\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyPhoneRequest;
use App\Models\User;
use App\UseCases\LoyaltyService;
use App\UseCases\PosService;
use JetBrains\PhpStorm\ArrayShape;
use Ramsey\Uuid\Uuid;

class RegisterService extends LoyaltyService
{
    function __construct(private readonly PosService $posService) {
        parent::__construct();
    }

    public function requestRegister(RegisterRequest $request): User
    {
        $phone = $request->get('phone');
        $data = $this->posService->getBalance($phone);
        if (isset($data['contactID']))
            throw new \DomainException('Существует контакт с таким телефоном', 111);

        $data = $request->validated();
        $user = User::firstOrNew(['phone' => $phone]);

        $fullName = explode(' ', $data['fullName']);
        $firstName = $fullName[1] ?? $fullName[0];
        $lastName = isset($fullName[1]) ? $fullName[0] : null;
        $middleName = $fullName[2] ?? null;

        $user->id = $user->id ?: Uuid::uuid4()->toString();
        $user->phone = $phone;
        $user->email = $data['email'] ?? null;
        $user->first_name = $firstName;
        $user->last_name = $lastName;
        $user->middle_name = $middleName;
        $user->password = $data['password'];
        $user->birth_date = $data['birthDate'];
        $user->gender = $data['gender'];

        if ($cardNumber = $request->get('cardNumber')) {
            $user->token = $this->phoneRegister($user, $cardNumber);
        }
        else {
            $user->id = $this->posService->createCard($user)['contactID'];
            $this->posService->getBalance($user->phone, true);
        }

        $user->save();

        return $user;
    }

    private function phoneRegister(User $user, string $cardNumber): string
    {
        $url = $this->urls['lk'] . '/Identity/RequestAdvancedPhoneEmailRegistration';
        $tmp = [
            'CardNumber' => $cardNumber,
            'MobilePhone' => $user->phone,
            'EmailAddress' => $user->email,
            'Firstname' => $user->first_name,
            'Lastname' => $user->last_name,
            'MiddleName' => $user->middle_name,
            'Password' => $user->password,
            'BirthDate' => $user->birth_date,
            'GenderCode' => $user->gender,
            'AllowNotification' => false,
            'AllowEmail' => false,
            'AllowSms' => false,
            'AgreeToTerms' => true,
            'PartnerId' => $this->config['partner_id']
        ];

        $response = $this->client->post($url, ['json' => ['parameter' => json_encode($tmp)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        return $data['value'];
    }

    public function requestPhoneVerification(string $token): string
    {
        $url = $this->urls['lk'] . '/Identity/RequestMobilePhoneVerification';
        $response = $this->client->post($url, ['json' => ['parameter' => json_encode(['Token' => $token])]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        return $data['value'];
    }

    #[ArrayShape(['id' => "string", 'sessionId' => "string"])]
    public function verifySms(VerifyPhoneRequest $request, string $token): array
    {
        $url = $this->urls['lk'] . '/Identity';
        $partnerId = $this->config['partner_id'];

        $data = [
            'Token' => $token,
            'Code' => $request->get('smsCode'),
            'PartnerId' => $partnerId
        ];

        $response = $this->client->post($url . '/CheckSmsForRegistration', ['json' => ['parameter' => json_encode($data)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        $data = [
            'Token' =>  $data['value'],
            'PartnerId' => $partnerId
        ];

        $response = $this->client->post($url . '/AdvancedPhoneEmailRegister', ['json' => ['parameter' => json_encode($data)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        return [
            'id' => $data['Id'],
            'sessionId' => $data['SessionId']
        ];
    }

}
