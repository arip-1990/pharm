<?php

namespace App\UseCases\User;

use App\UseCases\LoyaltyService;
use App\UseCases\ManagerService;
use App\UseCases\PosService;

class PhoneVerifyService extends LoyaltyService
{
    public function __construct(private readonly ManagerService $managerService, private readonly PosService $posService) {
        parent::__construct();
    }

    public function requestVerify(string $phone): string
    {
        $url = $this->urls['lk'] . '/Contact/SendSmsForMobilePhoneVerification';
        $manager = $this->managerService->login();
        $data = $this->posService->getBalance($phone);
        if (!isset($data['contactID']))
            throw new \DomainException('Нет клиента с таким номером');

        $data = [
            'Id' => $data['contactID'],
            'SessionId' => $manager['sessionId'],
            'PartnerId' => $this->config['partner_id']
        ];

        $response = $this->client->post($url, ['json' => ['parameter' => json_encode($data)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        return $data['value'];
    }

    public function verifyPhone(string $token, string $smsCode): string
    {
        $url = $this->urls['lk'] . '/Contact/VerifyMobilePhone';
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
