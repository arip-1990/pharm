<?php

namespace App\Listeners\Order;

use App\Models\Exception;
use App\Models\Order;
use App\Models\Status;
use App\Events\Order\OrderSend;
use App\Mail\Order\CreateOrder;
use App\UseCases\Order\GenerateDataService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OrderSendListener implements ShouldQueue
{
    public function handle(OrderSend $event): void
    {
        $order = $event->order;
        $order_number = config('data.orderStartNumber') + $order->id;

        try {
            $response = simplexml_load_string($this->getSendInfo($order));

            if(isset($response->errors->error->code)) {
                $message = 'Номер заказа: ' . $order_number . '. Код ошибки: ' . $response->errors->error->code . '.';

                throw new \DomainException($message . $response->errors->error->message);
            }

            if(isset($response->success->order_id)) {
                $order->changeStatusState(Status::STATE_SUCCESS);

                Mail::to(Auth::user())->send(new CreateOrder($order));
            }
        }
        catch (\Exception $e) {
            $order->changeStatusState(Status::STATE_ERROR);
            Exception::create($order->id, '1c', $e->getMessage())->save();
        }

        $order->save();
    }

    private function getSendInfo(Order $order): string
    {
        $service = new GenerateDataService($order);
        $config = config('data.1c');
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'http://' . $config['user'] . ':' . $config['password'] . '@' . $config['urls'][5],
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_POSTFIELDS => $service->generateSenData(new \DateTimeImmutable())
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
