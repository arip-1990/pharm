<?php

namespace App\Console\Commands;

use App\Events\Order\OrderSend;
use App\Models\Order;
use App\Models\Status;
use Illuminate\Console\Command;

ini_set('memory_limit', -1);

class TestCommand extends Command
{
    protected $signature = 'test {order}';
    protected $description = 'test';

    public function handle(): int
    {
        $orderId = $this->argument('order');
        /** @var Order $order */
        if (!$order = Order::query()->find((int)$orderId) and $order->status === Status::STATUS_SENT_IN_1C) {
            return 1;
        }

        $order->changeStatusState(Status::STATE_WAIT);
        OrderSend::dispatch($order);
        $order->save();

        $this->info('Процесс завершена!');
        return 0;
    }
}
