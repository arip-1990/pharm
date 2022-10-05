<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Status\OrderState;
use App\Models\Status\OrderStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class OrderCommand extends Command
{
    protected $signature = 'order {orderId} {type=send}';
    protected $description = 'test';

    public function handle(): int
    {
        $client = Redis::connection('bot')->client();
        if (!$order = Order::find((int)$this->argument('orderId'))) {
            $client->publish("bot:import", json_encode([
                'success' => false,
                'file' => 'OrderCommand',
                'line' => '19',
                'message' => 'Заказ не найден!'
            ]));
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
                $client->publish("bot:import", json_encode([
                    'success' => false,
                    'file' => 'OrderCommand',
                    'line' => '39',
                    'message' => 'Неверный тип!'
                ]));
                $this->error('Неверный тип!');
                return 1;
        }

        $client->publish("bot:import", json_encode(['success' => true, 'message' => 'Процесс завершен!']));
        $this->info('Процесс завершен!');
        return 0;
    }
}
