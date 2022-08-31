<?php

namespace App\UseCases\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyPhoneRequest;
use App\Models\User;
use App\UseCases\PosService;
use GuzzleHttp\Client;
use JetBrains\PhpStorm\ArrayShape;

class RegisterService
{
    private Client $client;

    public function __construct(private readonly PosService $posService) {
        $this->client = new Client([
            'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
            'http_errors' => false,
            'verify' => false
        ]);
    }

    public function requestRegister(RegisterRequest $request): void
    {
        $data = $this->posService->getBalance($request->get('phone'));
        if (isset($data['ContactID']))
            throw new \DomainException('Существует контакт с таким телефоном');

        $data = $request->validated();
        $user = User::query()->firstOrNew(['phone' => $data['phone'], 'email' => $data['email']]);
        $user->phone = $data['phone'];
        $user->email = $data['email'];
        $user->first_name = $data['firstName'];
        $user->last_name = $data['lastName'];
        $user->middle_name = $data['middleName'];
        $user->password = $data['password'];
        $user->birth_date = $data['birthDate'];
        $user->gender = $data['gender'];

        if ($cardNumber = $request->get('cardNumber')) {
            $user->token = $this->phoneRegister($user, $cardNumber);
            $request->session()->put('token', $user->token);
        }
        else {
            $data = $this->posService->createCard($user);
            $this->posService->getBalance($user->phone, true);

            $user->id = $data['contactID'];
            $request->session()->put('userId', $user->id);
        }

        $user->save();
    }

    private function phoneRegister(User $user, string $cardNumber): string
    {
        $url = config('data.loyalty.test.url.lk') . '/Identity/RequestAdvancedPhoneEmailRegistration';
        $partnerId = config('data.loyalty.test.partner_id');
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
            'PartnerId' => $partnerId
        ];

        $response = $this->client->post($url, ['json' => ['parameter' => json_encode($tmp)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        return $data['value'];
    }

    public function requestPhoneVerification(string $token): string
    {
        $url = config('data.loyalty.test.url.lk') . '/Identity/RequestMobilePhoneVerification';

        $response = $this->client->post($url, ['json' => ['parameter' => json_encode(['Token' => $token])]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        return $data['value'];
    }

    #[ArrayShape(['id' => "string", 'sessionId' => "string"])]
    public function verifySms(VerifyPhoneRequest $request, string $token): array
    {
        $url = config('data.loyalty.test.url.lk') . '/Identity/';
        $partnerId = config('data.loyalty.test.partner_id');

        $data = [
            'Token' => $token,
            'Code' => $request->get('smsCode'),
            'PartnerId' => $partnerId
        ];

        $response = $this->client->post($url . 'CheckSmsForRegistration', ['json' => ['parameter' => json_encode($data)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        $data = [
            'Token' =>  $data['value'],
            'PartnerId' => $partnerId
        ];

        $response = $this->client->post($url . 'AdvancedPhoneEmailRegister', ['json' => ['parameter' => json_encode($data)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        return [
            'id' => $data['Id'],
            'sessionId' => $data['SessionId']
        ];
    }

}
