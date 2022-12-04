<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderSend;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Status\OrderState;
use App\Models\Status\OrderStatus;
use App\UseCases\Order\GenerateDataService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderSendListener implements ShouldQueue
{
    public function handle(OrderSend $event): void
    {
        $order = $event->order;
        $orderNumber = config('data.orderStartNumber') + $order->id;

        if ($order->payment->isType(Payment::TYPE_CARD) and !$order->isPay() or $order->isSent())
            return;

        try {
            $response = simplexml_load_string($this->getSendInfo($order));

            if(isset($response->errors->error->code))
                throw new \DomainException("Номер заказа: {$orderNumber}. {$response->errors->error->message}");

            if(isset($response->success->order_id))
                $order->changeStatusState(OrderStatus::STATUS_SEND, OrderState::STATE_SUCCESS);
        }
        catch (\Exception $e) {
            $order->changeStatusState(OrderStatus::STATUS_SEND, OrderState::STATE_ERROR);
            throw new \DomainException($e->getMessage());
        } finally {
            $order->save();
        }
    }

    private function getSendInfo(Order $order): string
    {
        $service = new GenerateDataService($order);
        $config = config('data.1c');

        $client = new Client([
            'base_uri' => $config['base_url'],
            'auth' => [$config['login'], $config['password']],
            'verify' => false
        ]);
        $response = $client->post($config['urls'][5], ['body' => $service->generateSenData(Carbon::now())]);

        return $response->getBody()->getContents();
    }
}
