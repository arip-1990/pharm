<?php

namespace App\Order\Listener;

use App\Exceptions\OrderException;
use App\Order\SenderOrderData;
use Illuminate\Support\Facades\Redis;
use App\Order\Entity\Payment;
use App\Order\Entity\Status\{OrderState, OrderStatus};
use App\Order\Event\OrderSend;
use Illuminate\Contracts\Queue\ShouldQueue;

readonly class OrderSendListener implements ShouldQueue
{
    public function __construct(private SenderOrderData $sender) {}

    public function handle(OrderSend $event): void
    {
        $queueClient = Redis::connection('bot')->client();
        $order = $event->order;
        $orderNumber = config('data.orderStartNumber') + $order->id;

        if ($order->payment->isType(Payment::TYPE_CARD) and !$order->isPay() or $order->isSent())
            return;

        try {
            $response = simplexml_load_string($this->sender->send($order));

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
}
