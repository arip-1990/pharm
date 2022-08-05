<?php

namespace App\UseCases\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\UseCases\PosService;
use GuzzleHttp\Client;
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

    public function handle(RegisterRequest $request): void
    {
        try {
            $data = $this->posService->getBalance($request->get('phone'));
            dd($data);
            throw new \DomainException('Регистрация продолжена не будет, клиент с таким мобильным уже существует');
        }
        catch (\DomainException $e) {}

        if ($request->has('CardNumber')) {
            $token = $this->phoneRegister($request->validated());
            $request->session()->put('token', $token);
        }
        else {

        }
    }

    public function phoneRegister(array $data): string
    {
        $url = config('data.loyalty.test.url.lk') . '/Identity/RequestAdvancedPhoneEmailRegistration';
        $partnerId = config('data.loyalty.test.partner_id');
        $data = [
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

        $response = $this->client->post($url, ['body' => json_encode(['parameter' => $data])]);
        $data = json_decode($response->getBody());

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($response->getBody()->getContents());

        User::query()->create([
            'id' => Uuid::uuid4()->toString(),
            'phone' => $data['phone'],
            'email' => $data['email'],
            'first_name' => $data['firstName'],
            'last_name' => $data['lastName'],
            'middle_name' => $data['middleName'],
            'password' => $data['password'],
            'birth_date' => $data['birthDate'],
            'gender' => $data['gender'],
            'token' => $data['value'],
        ]);

        return $data['value'];
    }
}
