<?php

namespace App\Console\Commands\Send;

use App\Events\Order\OrderSend;
use App\Models\Order;
use App\Models\Status;
use Illuminate\Console\Command;

class OrderCommand extends Command
{
    protected $signature = 'send:order {order}';
    protected $description = 'sent order to 1c';

    public function handle(): int
    {
        $orderId = $this->argument('order');
        /** @var Order $order */
        if (!$order = Order::query()->find((int)$orderId) or $order->status !== Status::STATUS_SENT_IN_1C) {
            return 1;
        }

        $order->changeStatusState(Status::STATE_WAIT);
        $order->save();
        OrderSend::dispatch($order);

        $this->info('Отправка заказа добавлен в очередь!');
        return 0;
    }
}
