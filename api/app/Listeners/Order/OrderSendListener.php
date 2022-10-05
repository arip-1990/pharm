<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderSend;
use App\Mail\Order\CreateOrder;
use App\Models\Order;
use App\Models\Status\OrderState;
use App\Models\Status\OrderStatus;
use App\UseCases\Order\GenerateDataService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

class OrderSendListener implements ShouldQueue
{
    public function handle(OrderSend $event): void
    {
        $client = Redis::client();
        $order = $event->order;
        try {
            $order_number = config('data.orderStartNumber') + $order->id;
            $response = simplexml_load_string($this->getSendInfo($order));

            if(isset($response->errors->error->code)) {
                $message = 'Номер заказа: ' . $order_number . '. Код ошибки: ' . $response->errors->error->code;

                throw new \DomainException($message . '. ' . $response->errors->error->message);
            }

            $client->publish("bot:import", json_encode(['success' => true, 'message' => $response->success->order_id]));
            if(isset($response->success->order_id)) {
                $order->changeStatusState(OrderState::STATE_SUCCESS);
                $order->addStatus(OrderStatus::STATUS_SENT_MAIL);

                Mail::to($order->user)->send(new CreateOrder($order));
            }
        }
        catch (\Exception $e) {
            $client->publish("bot:import", json_encode([
                'success' => false,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]));
            $order->changeStatusState(OrderState::STATE_ERROR);
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
            CURLOPT_POSTFIELDS => $service->generateSenData(Carbon::now())
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
