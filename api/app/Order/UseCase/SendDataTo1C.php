<?php

namespace App\Order\UseCase;

use App\Order\Entity\Order;
use App\Order\SenderOrderData;
use Carbon\Carbon;
use GuzzleHttp\Client;

readonly class SendDataTo1C implements SenderOrderData
{
    public function __construct(private GenerateOrderDataService $generator) {}

    public function send(Order $order): string
    {
        $config = config('services.1c');
        $client = new Client([
            'base_uri' => $config['base_url'],
            'auth' => [$config['login'], $config['password']],
            'verify' => false
        ]);
        $response = $client->post($config['urls'][5], ['body' => $this->generator->generateFor1C($order, Carbon::now())]);

        return $response->getBody()->getContents();
    }
}
