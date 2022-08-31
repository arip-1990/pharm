<?php

namespace App\UseCases;

use App\Models\User;
use GuzzleHttp\Client;

class CardService
{
    private Client $client;

    public function __construct() {
        $this->client = new Client([
            'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
            'http_errors' => false,
            'verify' => false
        ]);
    }

    public function getAll(User $user)
    {
        $url = config('data.loyalty.test.url.lk') . '/Card/GetAllByContact';

        $response = $this->client->get($url, ['query' => "contactid='{$user->id}'&sessionid='{$user->session}'"]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);

        return $data['value'];
    }

    public function block(User $user, string $cardId): void
    {
        $url = config('data.loyalty.test.url.lk') . '/Contact/BlockCard';
        $data = [
            'Id' => $user->id,
            'CardId' => $cardId,
            'SessionId' => $user->session
        ];

        $response = $this->client->post($url, ['json' => ['parameter' => json_encode($data)]]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException($data['odata.error']['message']['value'], $data['odata.error']['code']);
    }
}
