<?php

namespace App\UseCases\Auth;

use App\UseCases\LoyaltyService;

class PasswordService extends LoyaltyService
{
    public function requestResetPassword(string $phone): string
    {
        $url = $this->urls['lk'] . '/User/RequestPasswordChange';
        $data = [
            'PhoneOrEmail' => $phone,
            'PartnerId' => $this->config['partner_id']
        ];

        $response = $this->client->post($url, ['json' => ['parameter' => json_encode($data)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        return $data['value'];
    }

    public function validateTempPassword(string $token, string $password): string
    {
        $url = $this->urls['lk'] . '/User/ValidateTempPassword';
        $data = [
            'Token' => $token,
            'TemporaryPassword' => $password
        ];

        $response = $this->client->post($url, ['json' => ['parameter' => json_encode($data)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        return $data['value'];
    }

    public function changePassword(string $token, string $password): void
    {
        $url = $this->urls['lk'] . '/User/ChangePassword';
        $data = [
            'Token' => $token,
            'Password' => $password
        ];

        $response = $this->client->post($url, ['json' => ['parameter' => json_encode($data)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);
    }

}
