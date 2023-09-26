<?php

namespace App\Order;

use App\Order\Entity\Order;
use Carbon\Carbon;
use GuzzleHttp\Client;

trait DataSender
{
    private readonly GenerateOrderData $generator;

    public function sendData(Order $order): string
    {
        $config = config('services.1c');
        $client = new Client([
            'base_uri' => $config['base_url'],
            'auth' => [$config['login'], $config['password']],
            'verify' => false
        ]);
        $response = $client->post($config['urls'][5], ['body' => $this->generator->generate($order, Carbon::now())]);

        return $response->getBody()->getContents();
    }
}
