<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderSend;
use App\Mail\Order\CreateOrder;
use App\Models\Order;
use App\Models\Status\OrderState;
use App\Models\Status\OrderStatus;
use App\UseCases\Order\GenerateDataService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class OrderSendListener implements ShouldQueue
{
    public function handle(OrderSend $event): void
    {
        $order = $event->order;
        $status = OrderStatus::STATUS_SEND;
        try {
            $orderNumber = config('data.orderStartNumber') + $order->id;
            $response = simplexml_load_string($this->getSendInfo($order));

            if(isset($response->errors->error->code)) {
                $message = 'Номер заказа: ' . $orderNumber . '. Код ошибки: ' . $response->errors->error->code;

                throw new \DomainException($message . '. ' . $response->errors->error->message);
            }

            if(isset($response->success->order_id)) {
                $order->changeStatusState($status, OrderState::STATE_SUCCESS);

                $status = OrderStatus::STATUS_MESSAGE;
                $order->addStatus($status);
                Mail::to($order->user)->send(new CreateOrder($order));
                $order->changeStatusState($status, OrderState::STATE_SUCCESS);
            }
        }
        catch (\Exception $e) {
            $order->changeStatusState($status, OrderState::STATE_ERROR);
            throw new \DomainException($e->getMessage());
        } finally {
            $order->save();
        }
    }

    private function getSendInfo(Order $order): string
    {
        $service = new GenerateDataService($order);
        $config = config('data.1c');

        $client = new Client([
            'base_uri' => $config['base_url'],
            'auth' => [$config['login'], $config['password']],
            'verify' => false
        ]);
        $response = $client->post($config['urls'][5], ['body' => $service->generateSenData(Carbon::now())]);

        return $response->getBody()->getContents();
    }
}
