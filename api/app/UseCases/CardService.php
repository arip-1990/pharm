<?php

namespace App\UseCases;

use App\Models\User;

class CardService extends LoyaltyService
{
    public function getAll(User $user, string $session)
    {
        $url = $this->urls['lk'] . '/Card/GetAllByContact';
        $response = $this->client->get($url, ['query' => "contactid='{$user->id}'&sessionid='{$session}'"]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        return $data['value'];
    }

    public function block(User $user, string $cardId, string $session): void
    {
        $url = $this->urls['lk'] . '/Contact/BlockCard';
        $data = [
            'Id' => $user->id,
            'CardId' => $cardId,
            'SessionId' => $session
        ];

        $response = $this->client->post($url, ['json' => ['parameter' => json_encode($data)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);
    }
}
