<?php

namespace App\UseCases;

use App\Models\Order;
use GuzzleHttp\Client;
use JetBrains\PhpStorm\ArrayShape;

class AcquiringService
{
    public function __construct(private readonly Client $client) {}

    #[ArrayShape(['paymentId' => "string", 'paymentUrl' => "string"])]
    public function sberPay(int $orderId, string $successUrl = null, string $failUrl = null): array
    {
        $config =  config('app.env') === 'production' ? config('data.pay.sber.prod') : config('data.pay.sber.test');
        if (!$order = Order::find($orderId))
            throw new \DomainException('Не найден заказ');

        $data = [
            'userName'      => $config['username'],
            'password'      => $config['password'],
            'orderNumber'   => $order->id,
            'amount'        => $order->getTotalCost() * 100,
            'returnUrl'     => $successUrl ?? 'https://xn--12080-6ve4g.xn--p1ai/order/checkout/' . $order->id,
        ];

        if ($failUrl) $data['failUrl'] = $failUrl;

        $response = $this->client->post($config['url'], ['query' => $data]);
        $data = json_decode($response->getBody(), true);

        if ($response->getStatusCode() !== 200 or isset($data['errorCode']))
            throw new \DomainException('Не удалось создать форму оплаты. ' . $data['errorMessage']);

        $order->pay($data['orderId']);
        $order->save();

        return [
            'paymentId' => $data['orderId'],
            'paymentUrl' => $data['formUrl']
        ];
    }
}
