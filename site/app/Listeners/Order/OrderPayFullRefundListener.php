<?php

namespace App\Listeners\Order;

use App\Entities\Exception;
use App\Entities\Order;
use App\Entities\Status;
use App\Events\Order\OrderPayFullRefund;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OrderPayFullRefundListener implements ShouldQueue
{
    public function handle(OrderPayFullRefund $event): void
    {
        $order = $event->order;
        if ($order->payment_type === Order::PAYMENT_TYPE_SBER and $order->isPay() and !$order->isFullRefund()) {
            $response = $this->getOrderInfo($order->sber_id);
            if($response['errorCode'] == 0) {
                $message = 'Успешно! sber_id: ' . $order->sber_id;
            }
            elseif (!isset($response['errorCode'])) {
                $message = 'Не удалось получить ответ от сервера. sber_id: ' . $order->sber_id;
                Exception::create($order->id, 'full-refund', $message)->save();
                throw new \DomainException($message);
            }
            else {
                $message = 'Ошибка! ' . $response['errorMessage'] . ', sber_id: ' . $order->sber_id;
                $order->changeStatusState(Status::STATE_ERROR);
                $order->save();

                Exception::create($order->id, 'full-refund', $message)->save();
                throw new \DomainException($message);
            }
        }

        $order->delete();
    }

    private function getOrderInfo(string $order_id): array
    {
        $config = config('data.pay.sber.prod');
        $url = $config['refundUrl'];
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
                'amount'        => 0,
                'orderId'   => $order_id
            ])
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }
}
