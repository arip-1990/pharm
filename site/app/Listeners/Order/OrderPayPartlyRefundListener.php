<?php

namespace App\Listeners\Order;

use App\Entities\Exception;
use App\Entities\Status;
use App\Events\Order\OrderPayPartlyRefund;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OrderPayPartlyRefundListener implements ShouldQueue
{
    public function handle(OrderPayPartlyRefund $event): void
    {
        $order = $event->order;
        if (!$order->isPay() or $order->isPartlyRefund()) {
            $message = 'Заказ не оплачен или возмещен.';
            Exception::create($order->id, 'partly-refund', $message)->save();
            throw new \DomainException($message);
        }

        if(!$difference = $order->getDifferenceOfRefund()) {
            $message = 'Сумма возврата не должна быть равна сумме заказа при частичном возврате! sber_id: ' . $order->sber_id;
            Exception::create($order->id, 'partly-refund', $message)->save();
            throw new \DomainException($message);
        }

        $response = $this->getOrderInfo($order->sber_id, $difference);
        if(!isset($response['errorCode'])) {
            $message = 'Не удалось получить ответ от сервера. sber_id: ' . $order->sber_id;
            Exception::create($order->id, 'partly-refund', $message)->save();
            throw new \DomainException($message);
        }
        elseif ($response['errorCode'] == 0) {
            $order->changeStatusState(Status::STATE_SUCCESS);
            $order->save();
        }
        else {
            $message = 'Ошибка! ' . $response['errorMessage'].', sber_id: ' . $order->sber_id;
            $order->changeStatusState(Status::STATE_ERROR);
            $order->save();

            Exception::create($order->id, 'partly-refund', $message)->save();
            throw new \DomainException($message);
        }
    }

    private function getOrderInfo(string $orderId, int $difference): array
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
                'amount'        => $difference * 100,
                'orderId'   => $orderId
            ])
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }
}
