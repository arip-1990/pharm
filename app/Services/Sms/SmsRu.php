<?php

namespace App\Services\Sms;

use GuzzleHttp\Client;

class SmsRu implements SmsSender
{
    private Client $client;

    public function __construct(private string $appId, private string $url = 'https://sms.ru/sms/send')
    {
        $this->client = new Client();
    }

    public function send(string $number, string $text): void
    {
        $this->client->post($this->url, [
            'form_params' => [
                'api_id' => $this->appId,
                'to' => '+' . trim($number, '+'),
                'text' => $text
            ]
        ]);
    }
}
