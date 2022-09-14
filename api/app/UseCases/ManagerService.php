<?php

namespace App\UseCases;

use JetBrains\PhpStorm\ArrayShape;

class ManagerService extends LoyaltyService
{
    #[ArrayShape(['id' => "string", 'sessionId' => "string"])]
    public function login(): array
    {
        $url = $this->urls['manager'] . '/Identity/Login';
        $data = ['Login' => $this->config['manager']['login'], 'Password' => $this->config['manager']['password']];

        $response = $this->client->post($url, ['json' => ['parameter' => json_encode($data)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        return ['id' => $data['Id'], 'sessionId' => $data['SessionId']];
    }
}
