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

class OrderSendListener implements ShouldQueue
{
    public function handle(OrderSend $event): void
    {
        $order = $event->order;
        try {
            $orderNumber = config('data.orderStartNumber') + $order->id;
            $response = simplexml_load_string($this->getSendInfo($order));

            if(isset($response->errors->error->code)) {
                $message = 'Номер заказа: ' . $orderNumber . '. Код ошибки: ' . $response->errors->error->code;

                throw new \DomainException($message . '. ' . $response->errors->error->message);
            }

            if(isset($response->success->order_id)) {
                $order->changeStatusState(OrderState::STATE_SUCCESS);
                $order->addStatus(OrderStatus::STATUS_SENT_MAIL);

                Mail::to($order->user)->send(new CreateOrder($order));
            }
        }
        catch (\Exception $e) {
            $order->changeStatusState(OrderState::STATE_ERROR);
            throw new \DomainException($e->getMessage());
        } finally {
            $order->save();
        }
    }

    private function getSendInfo(Order $order): string
    {
        $service = new GenerateDataService($order);
        $config = config('data.1c');
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'http://' . $config['login'] . ':' . $config['password'] . '@' . $config['urls'][5],
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
