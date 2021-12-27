<?php

namespace App\Jobs;

use App\Entities\Exception;
use App\Entities\Order;
use App\Entities\Status;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OrderPay implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private Order $order) {}

    public function handle(): void
    {
        if ($this->order->status !== Status::STATUS_PAID or $this->order->payment_type !== Order::PAYMENT_TYPE_SBERBANK) {
            $message = 'Не ожидает оплаты.';
            $this->order->changeStatusState(Status::STATE_ERROR);
            $this->order->save();
            Exception::create($this->order->id, 'sber', $message)->save();
            return;
        }

        if(!$this->order->sber_id) {
            $message = 'Нет идентификатора сбербанка.';
            $this->order->changeStatusState(Status::STATE_ERROR);
            $this->order->save();
            Exception::create($this->order->id, 'sber', $message)->save();
            return;
        }

        $orderInfo = $this->getOrderInfo();
        if($orderInfo['ErrorCode'] != 0)
            $this->order->changeStatusState(Status::STATE_ERROR);
        elseif($orderInfo['OrderStatus'] == Status::SBERBANK_ORDER_AUTH_REJECTED or $orderInfo['OrderStatus'] == Status::SBERBANK_ORDER_AUTH_CANCELLED)
            $this->order->changeStatusState(Status::STATE_ERROR);
        elseif($this->order->getTotalCost() != $orderInfo['Amount'] / 100)
            $this->order->changeStatusState(Status::STATE_ERROR);
        elseif($orderInfo['OrderStatus'] == Status::SBERBANK_ORDER_AUTH_DEPOSIT) {
            $this->order->changeStatusState(Status::STATE_SUCCESS);
            $this->order->sent();
        }
        else
            throw new \DomainException('Не получен ответ от сбербанка');

        $this->order->save();
    }

    private function getOrderInfo(): array
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
                'orderId'       => $this->order->sber_id
            ])
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }
}
