<?php

namespace App\UseCases\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyPhoneRequest;
use App\UseCases\PosService;
use Carbon\Carbon;
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
            throw new \DomainException('Клиент с таким мобильным уже существует');

        $data = $request->validated();
        $request->session()->put('userData', [
            'phone' => $data['phone'],
            'email' => $data['email'],
            'first_name' => $data['firstName'],
            'last_name' => $data['lastName'],
            'middle_name' => $data['middleName'],
            'password' => $data['password'],
            'birth_date' => $data['birthDate'],
            'gender' => $data['gender'],
        ]);

        if ($request->has('cardNumber')) {
            $token = $this->phoneRegister($data);
            $request->session()->put('token', $token);
        }
        else {
            $phone = $data['phone'];
            $this->posService->createCard($phone, $data['email'], $data['firstName'], $data['lastName'], $data['middleName'], Carbon::parse($data['birthDate']));
            $this->posService->getBalance($phone, true);
        }
    }

    private function phoneRegister(array $data): string
    {
        $url = config('data.loyalty.test.url.lk') . '/Identity/RequestAdvancedPhoneEmailRegistration';
        $partnerId = config('data.loyalty.test.partner_id');
        $tmp = [
            'CardNumber' => $data['cardNumber'],
            'MobilePhone' => $data['phone'],
            'EmailAddress' => $data['email'],
            'Firstname' => $data['firstName'],
            'Lastname' => $data['lastName'],
            'MiddleName' => $data['middleName'],
            'Password' => $data['password'],
            'BirthDate' => $data['birthDate'],
            'GenderCode' => $data['gender'],
            'AllowNotification' => false,
            'AllowEmail' => false,
            'AllowSms' => false,
            'AgreeToTerms' => true,
            'PartnerId' => $partnerId
        ];

        $response = $this->client->post($url, ['body' => json_encode(['parameter' => $tmp])]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        return $data['value'];
    }

    public function requestPhoneVerification(string $token): string
    {
        $url = config('data.loyalty.test.url.lk') . '/Identity/RequestMobilePhoneVerification';

        $response = $this->client->post($url, ['body' => json_encode(['parameter' => ['Token' => $token]])]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        return $data['value'];
    }

    #[ArrayShape(['id' => "string", 'sessionId' => "string"])]
    public function verifySms(VerifyPhoneRequest $request, string $token): array
    {
        $url = config('data.loyalty.test.url.lk') . '/Identity';
        $partnerId = config('data.loyalty.test.partner_id');

        $data = [
            'Token' => $token,
            'Code' => $request->get('smsCode'),
            'PartnerId' => $partnerId
        ];

        $response = $this->client->post($url . '/CheckSmsForRegistration', ['body' => json_encode(['parameter' => $data])]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        $data = [
            'Token' =>  $data['Token'],
            'PartnerId' => $partnerId
        ];

        $response = $this->client->post($url . '/AdvancedPhoneEmailRegister', ['body' => json_encode(['parameter' => $data])]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        return [
            'id' => $data['Id'],
            'sessionId' => $data['SessionId']
        ];
    }

}
