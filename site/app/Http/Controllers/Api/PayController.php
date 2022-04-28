<?php

namespace App\Http\Controllers\Api;

use App\Models\Exception;
use App\Models\Order;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class PayController
{
    public function handle(Request $request): Response
    {
        Redis::publish('bot:pay', json_encode($request->all()));

        $data = 'amount;' . $request->get('amount') . ';mdOrder;' . $request->get('mdOrder')
            . ';operation;' . $request->get('operation') . ';status;' . $request->get('status') . ';';

        $publicKey = Storage::get('callback.cer');

        $binarySignature = hex2bin(strtolower($request->get('checksum')));
        $isVerify = openssl_verify($data, $binarySignature, $publicKey, OPENSSL_ALGO_SHA512);

        Redis::publish('bot:pay', 'verified => ' . $isVerify);

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
        if ($order->status === Status::STATUS_PAID) {
            switch ($operation) {
                case 'deposited':
                    if ($status === 1) {
                        $order->changeStatusState(Status::STATE_SUCCESS);
                        $order->sent();
                    }
                    elseif ($status === 0) {
                        $order->changeStatusState(Status::STATE_ERROR);
                    }
                    break;
                case 'reversed':
                    $order->changeStatusState(Status::STATE_ERROR);
                    Exception::create($order->id, 'pay', 'Оплата отменена')->save();
                    break;
                case 'declinedByTimeout':
                    $order->changeStatusState(Status::STATE_ERROR);
                    Exception::create($order->id, 'pay', 'Истек время ожидания оплаты')->save();
                    break;
            }
        }
        else {
            Redis::publish('bot:pay', 'Данный заказ не ожидает оплаты!');
        }
    }
}
