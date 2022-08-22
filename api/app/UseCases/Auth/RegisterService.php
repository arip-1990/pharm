<?php

namespace App\UseCases\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyPhoneRequest;
use App\Models\User;
use App\UseCases\PosService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

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
        if (array_key_exists('ContactID', $data))
            throw new \DomainException('Регистрация продолжена не будет, клиент с таким мобильным уже существует');

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

        if ($request->has('CardNumber')) {
            $token = $this->phoneRegister($data);
            $request->session()->put('token', $token);
        }
        else {
            $phone = $data['phone'];
            $data = $this->posService->createCard($phone, $data['email'], $data['firstName'], $data['lastName'], $data['middleName'], Carbon::parse($data['birthDate']));
            if ($data['ReturnCode'] !== 0)
                throw new \DomainException($data['Message']);

            $request->session()->put('cardNumber', $data['CardNumber']);

            $data = $this->posService->getBalance($phone, true);
            if ($data['ReturnCode'] !== 0)
                throw new \DomainException($data['Message']);
        }
    }

    private function phoneRegister(array $data): string
    {
        $url = config('data.loyalty.test.url.lk') . '/Identity/RequestAdvancedPhoneEmailRegistration';
        $partnerId = config('data.loyalty.test.partner_id');
        $tmp = [
            'CardNumber' => $data['cardNumber'],
            'MobilePhone' => '+' . $data['phone'],
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
        if ($response->getStatusCode() !== 200)
            throw new \DomainException($response->getBody()->getContents());

        $data = json_decode($response->getBody(), true);
        return $data['value'];
    }

    public function verifySms(VerifyPhoneRequest $request): string
    {
        $url = config('data.loyalty.test.url.lk') . '/Identity';
        $partnerId = config('data.loyalty.test.partner_id');

        $data = [
            'Token' => $request->session()->get('token'),
            'Code' => $request->get('smsCode'),
            'PartnerId' => $partnerId
        ];

        $response = $this->client->post($url . '/CheckSmsForRegistration', ['body' => json_encode(['parameter' => $data])]);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($response->getBody()->getContents());

        $token = json_decode($response->getBody(), true)['Token'];
        $data = [
            'Token' =>  $token,
            'PartnerId' => $partnerId
        ];

        $response = $this->client->post($url . '/AdvancedPhoneEmailRegister', ['body' => json_encode(['parameter' => $data])]);
        if ($response->getStatusCode() !== 200)
            throw new \DomainException($response->getBody()->getContents());

        $data = json_decode($response->getBody(), true);
        return $data['Id'];
    }

}
