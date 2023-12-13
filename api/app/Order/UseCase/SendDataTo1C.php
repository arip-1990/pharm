<?php

namespace App\Order\UseCase;

use App\Order\Entity\Order;
use App\Order\Entity\Payment;
use App\Order\Entity\Status\OrderStatus;
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

    public function checkOrder(Order $order): bool
    {
        return !($order->isSent() or $order->isStatusSuccess(OrderStatus::STATUS_PROCESSING) or ($order->payment->isType(Payment::TYPE_CARD) and !$order->isPay()));
    }
}
