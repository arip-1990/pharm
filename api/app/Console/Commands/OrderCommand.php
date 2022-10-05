<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Status\OrderState;
use App\Models\Status\OrderStatus;
use Illuminate\Console\Command;

class OrderCommand extends Command
{
    protected $signature = 'order {orderId} {type=send}';
    protected $description = 'test';

    public function handle(): int
    {
        if (!$order = Order::find((int)$this->argument('orderId'))) {
            $this->error('Заказ не найден!');
            return 1;
        }

        switch ($this->argument('type')) {
            case 'send':
                if ($order->status === OrderStatus::STATUS_PAID) {
                    $order->changeStatusState(OrderState::STATE_SUCCESS);
                }
                $order->sent();
                $order->save();
                break;
            default:
                $this->error('Неверный тип!');
        }

        $this->info('Процесс завершена!');
        return 0;
    }
}
