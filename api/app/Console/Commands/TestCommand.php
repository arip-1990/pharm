<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

ini_set('memory_limit', -1);

class TestCommand extends Command
{
    protected $signature = 'test';
    protected $description = 'test';

    public function handle(): int
    {
        dump(Storage::path('.'));
        dd(Storage::directories('.'));
//        $order = Order::find((int)$this->argument('orderId'));
//        $order->addStatus(OrderStatus::STATUS_SEND);
//        try {
//            $orderNumber = config('data.orderStartNumber') + $order->id;
//            $response = simplexml_load_string($this->orderSend($order));
//
//            if(isset($response->errors->error->code))
//                throw new \DomainException("Номер заказа: {$orderNumber}. {$response->errors->error->message}");
//
//            if(isset($response->success->order_id))
//                $order->changeStatusState(OrderStatus::STATUS_SEND, OrderState::STATE_SUCCESS);
//        }
//        catch (\Exception $e) {
//            $order->changeStatusState(OrderStatus::STATUS_SEND, OrderState::STATE_ERROR);
//            Log::info($e->getMessage());
//
//            $queueClient->publish('bot:error', json_encode([
//                'file' => self::class . ' (' . $e->getLine() . ')',
//                'message' => $e->getMessage()
//            ], JSON_UNESCAPED_UNICODE));
//        } finally {
//            $order->save();
//        }
        return self::SUCCESS;
    }
}
