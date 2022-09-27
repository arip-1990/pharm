<?php

namespace App\UseCases;

use App\Models\Order;
use GuzzleHttp\Client;

class AcquiringService
{
    public function __construct(private readonly Client $client) {}

    public function sber(int $orderId, string $redirectUrl): string
    {
        $config =  config('app.env') === 'production' ? config('data.pay.sber.prod') : config('data.pay.sber.test');
        if (!$order = Order::find($orderId))
            throw new \DomainException('Не найден заказ');

        $data = [
            'userName'      => $config['username'],
            'password'      => $config['password'],
            'orderNumber'   => $order->id,
            'amount'        => $order->getTotalCost() * 100,
            'returnUrl'     => $redirectUrl,
        ];

        $response = $this->client->post($config['url'], ['json' => $data]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200)
            throw new \DomainException('Не удалось создать форму оплаты. ' . $data['errorMessage']);

        $order->pay($data['orderId']);
        $order->save();

        return $data['formUrl'];
    }
}
