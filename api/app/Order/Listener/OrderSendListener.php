<?php

namespace App\Order\Listener;

use App\Exceptions\OrderException;
use Illuminate\Support\Facades\Redis;
use App\Order\Entity\{Order, Payment};
use App\Order\Entity\Status\{OrderState, OrderStatus};
use App\Order\Event\OrderSend;
use App\Order\UseCase\GenerateDataService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderSendListener implements ShouldQueue
{
    public function handle(OrderSend $event): void
    {
        $queueClient = Redis::connection('bot')->client();
        $order = $event->order;
        $orderNumber = config('data.orderStartNumber') + $order->id;

        if ($order->payment->isType(Payment::TYPE_CARD) and !$order->isPay() or $order->isSent())
            return;

        try {
            $response = simplexml_load_string($this->getSendInfo($order));

            if(isset($response->errors->error->code))
                throw new OrderException("Номер заказа: {$orderNumber}. {$response->errors->error->message}");

            if(isset($response->success->order_id))
                $order->changeStatusState(OrderStatus::STATUS_SEND, OrderState::STATE_SUCCESS);
        }
        catch (\Exception $e) {
            $order->changeStatusState(OrderStatus::STATUS_SEND, OrderState::STATE_ERROR);

            $queueClient->publish('bot:error', json_encode([
                'file' => self::class . ' (' . $e->getLine() . ')',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));

            throw new OrderException($e->getMessage());
        } finally {
            $order->save();
        }
    }

    private function getSendInfo(Order $order): string
    {
        $service = new GenerateDataService($order);
        $config = config('services.1c');

        $client = new Client([
            'base_uri' => $config['base_url'],
            'auth' => [$config['login'], $config['password']],
            'verify' => false
        ]);
        $response = $client->post($config['urls'][5], ['body' => $service->generateSenData(Carbon::now())]);

        return $response->getBody()->getContents();
    }
}
