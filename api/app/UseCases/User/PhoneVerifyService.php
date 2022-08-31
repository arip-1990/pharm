<?php

namespace App\UseCases\User;

use App\UseCases\ManagerService;
use App\UseCases\PosService;
use GuzzleHttp\Client;

class PhoneVerifyService
{
    private Client $client;

    public function __construct(private readonly ManagerService $managerService, private readonly PosService $posService) {
        $this->client = new Client([
            'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
            'http_errors' => false,
            'verify' => false
        ]);
    }

    public function requestVerify(string $phone): string
    {
        $url = config('data.loyalty.test.url.lk') . '/Contact/SendSmsForMobilePhoneVerification';
        $partnerId = config('data.loyalty.test.partner_id');

        $manager = $this->managerService->login();
        $data = $this->posService->getBalance($phone);
        if (!isset($data['ContactID']))
            throw new \DomainException('Нет клиента с таким номером');

        $data = [
            'Id' => $data['ContactID'],
            'SessionId' => $manager['sessionId'],
            'PartnerId' => $partnerId
        ];

        $response = $this->client->post($url, ['json' => ['parameter' => json_encode($data)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        return $data['value'];
    }

    public function verifyPhone(string $token, string $smsCode): string
    {
        $url = config('data.loyalty.test.url.lk') . '/Contact/VerifyMobilePhone';
        $manager = $this->managerService->login();

        $data = [
            'Code' => $smsCode,
            'Token' => $token,
            'SessionId' => $manager['sessionId']
        ];

        $response = $this->client->post($url, ['json' => ['parameter' => json_encode($data)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        return $data['value'];
    }
}
