<?php

namespace App\Listeners\Order;

use App\Models\Exception;
use App\Models\Order;
use App\Models\Status;
use App\Events\Order\OrderPay;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OrderPayListener implements ShouldQueue
{
    public function handle(OrderPay $event): void
    {
        $order = $event->order;
        if ($order->status !== Status::STATUS_PAID or $order->payment_type !== Order::PAYMENT_TYPE_SBER) {
            $message = 'Не ожидает оплаты.';
            $order->changeStatusState(Status::STATE_ERROR);
            $order->save();
            Exception::create($order->id, 'sber', $message)->save();
            return;
        }

        if(!$order->sber_id) {
            $message = 'Нет идентификатора сбербанка.';
            $order->changeStatusState(Status::STATE_ERROR);
            $order->save();
            Exception::create($order->id, 'sber', $message)->save();
            return;
        }

        $orderInfo = $this->getOrderInfo($order);
        if($orderInfo['ErrorCode'] != 0)
            $order->changeStatusState(Status::STATE_ERROR);
        elseif($orderInfo['OrderStatus'] == Status::SBERBANK_ORDER_AUTH_REJECTED or $orderInfo['OrderStatus'] == Status::SBERBANK_ORDER_AUTH_CANCELLED)
            $order->changeStatusState(Status::STATE_ERROR);
        elseif($order->getTotalCost() != $orderInfo['Amount'] / 100)
            $order->changeStatusState(Status::STATE_ERROR);
        elseif($orderInfo['OrderStatus'] == Status::SBERBANK_ORDER_AUTH_DEPOSIT) {
            $order->changeStatusState(Status::STATE_SUCCESS);
            $order->sent();
        }
        else
            throw new \DomainException('Не получен ответ от сбербанка');

        $order->save();
    }

    private function getOrderInfo(Order $order): array
    {
        $config = config('data.pay.sber.prod');
        $url = $config['statusUrl'];
        $username = $config['username'];
        $password = $config['password'];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'userName'      => $username,
                'password'      => $password,
                'orderId'       => $order->sber_id
            ])
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }
}
