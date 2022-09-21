<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderPayPartlyRefund;
use App\Models\Status\OrderState;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderPayPartlyRefundListener implements ShouldQueue
{
    public function handle(OrderPayPartlyRefund $event): void
    {
        $order = $event->order;
        try {
            if (!$order->isPay() or $order->isPartlyRefund())
                throw new \DomainException('Заказ не оплачен или возмещен.');

            if(!$difference = $order->getDifferenceOfRefund())
                throw new \DomainException('Сумма возврата не должна быть равна сумме заказа при частичном возврате! sber_id: ' . $order->sber_id);

            $response = $this->getOrderInfo($order->sber_id, $difference);
            if(!isset($response['errorCode']))
                throw new \DomainException('Не удалось получить ответ от сервера. sber_id: ' . $order->sber_id);
            elseif ($response['errorCode'] != 0)
                throw new \DomainException('Ошибка! ' . $response['errorMessage'].', sber_id: ' . $order->sber_id);

            $order->changeStatusState(OrderState::STATE_SUCCESS);
        }
        catch (\Exception $exception) {
            $order->changeStatusState(OrderState::STATE_ERROR);
        }

        $order->save();
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
