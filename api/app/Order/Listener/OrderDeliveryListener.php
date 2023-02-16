<?php

namespace App\Order\Listener;

use App\Order\Event\OrderDelivery;
use App\Order\Entity\Delivery;
use App\Order\Entity\Status\{OrderState, OrderStatus};
use App\Order\UseCase\GenerateDataService;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderDeliveryListener implements ShouldQueue
{
    public function __construct(private readonly GenerateDataService $service) {}

    public function handle(OrderDelivery $event): void
    {
        $order = $event->order;
//        if ($order->delivery->equalType(Delivery::TYPE_PICKUP)) {
//            $message = 'Заказ не может быть отправлен.';
//            $order->changeStatusState(OrderState::STATE_ERROR);
//            $order->save();
//            return;
//        }
//
//        $number_order = config('data.orderStartNumber') + $order->id;
//        $response = $this->getDeliveryInfo($number_order, $this->service->generateDeliveryData());
//        if(isset($response['code'])) {
//            $message = 'Номер заказа(1c): ' . $number_order . '. Code: ' . $response['code'] . '. -> ' . $response['message'];
//            $order->changeStatusState(OrderState::STATE_ERROR);
//            $order->save();
//            throw new \DomainException($message);
//        }
//        if(isset($response['id'])) {
//            $order->yandex_id = strval($response['id']);
//            $order->changeStatusState(OrderState::STATE_SUCCESS);
//            $order->save();
//        }
    }

    private function getDeliveryInfo(int $orderId, string $data): array
    {
        $config = config('data.yandex.delivery');
        $query = ['request_id' => $config['idempotency_prefix'] . $orderId];
        $headers = ['Authorization: Bearer '.$config['auth_token'], 'Accept-Language: ru'];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $config['links']['create'] . '?' . http_build_query($query),
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $data
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}
