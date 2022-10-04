<?php

namespace App\Http\Controllers\V1;

use App\Models\Order;
use App\Models\Status\OrderState;
use App\Models\Status\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class PayController
{
    public function handle(Request $request): Response
    {
        $data = '';
        foreach ($request->collect()->except('checksum', 'sign_alias')->sortKeys() as $key => $value) {
            $data .= $key . ';' . $value . ';';
        }

        $publicKey = Storage::get('callback.cer');
        $binarySignature = hex2bin(strtolower($request->get('checksum')));
        $isVerify = openssl_verify($data, $binarySignature, $publicKey, OPENSSL_ALGO_SHA512);

        if ($isVerify !== 1) {
            return new Response(status: SymfonyResponse::HTTP_PRECONDITION_FAILED);
        }

        /** @var Order $order */
        $order = Order::query()->find((int)$request->get('orderNumber'));
        $this->checkStatus($order, $request->get('operation'), (int)$request->get('status'));
        $order->save();

        return new Response();
    }

    private function checkStatus(Order $order, string $operation, int $status): void
    {
        $client = Redis::client();
        $client->publish('bot:pay', "Статус: " . ($order->status === OrderStatus::STATUS_PAID));
//        if ($order->status === OrderStatus::STATUS_PAID) {
            switch ($operation) {
                case 'deposited':
                    if ($status === 1) {
                        $order->changeStatusState(OrderState::STATE_SUCCESS);
                        $order->sent();
                    }
                    elseif ($status === 0) {
                        $order->changeStatusState(OrderState::STATE_ERROR);
                    }
                    break;
                case 'reversed':
                    $order->changeStatusState(OrderState::STATE_ERROR);
                    $client->publish('bot:pay', "Заказ №{$order->id}: Оплата отменена!");
                    break;
                case 'declinedByTimeout':
                    $order->changeStatusState(OrderState::STATE_ERROR);
                    $client->publish('bot:pay', "Заказ №{$order->id}: Истек время ожидания оплаты!");
                    break;
            }
//        }
//        else {
//            $client->publish('bot:pay', "Заказ №{$order->id} не ожидает оплаты!");
//        }
    }
}
