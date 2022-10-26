<?php

namespace App\UseCases;

class CardService extends LoyaltyService
{
    public function __construct(private readonly ManagerService $managerService) {
        parent::__construct();
    }

    public function getAllByUser(string $userId, string $session = null): array
    {
        $url = $this->urls['lk'] . '/Card/GetAllByContact';
        $session = $session ?? $this->managerService->login()['sessionId'];

        $response = $this->client->get($url, ['query' => "contactid='{$userId}'&sessionid='{$session}'"]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        return $data['value'];
    }

    public function block(string $userId, string $cardId, string $session = null): void
    {
        $url = $this->urls['lk'] . '/Contact/BlockCard';
        $session = $session ?? $this->managerService->login()['sessionId'];
        $data = [
            'Id' => $userId,
            'CardId' => $cardId,
            'SessionId' => $session
        ];

        $response = $this->client->post($url, ['json' => ['parameter' => json_encode($data)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);
    }
}
