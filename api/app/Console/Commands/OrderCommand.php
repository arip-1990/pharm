<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class OrderCommand extends Command
{
    protected $signature = 'order {orderId} {type=send}';
    protected $description = 'test';

    public function handle(): int
    {
        $client = Redis::connection('bot')->client();
        if (!$order = Order::find((int)$this->argument('orderId')))
            throw new \DomainException('Заказ не найден!');

        switch ($this->argument('type')) {
            case 'send':
                $order->sent();
                $order->save();
                break;
            default:
                throw new \DomainException('Неверный тип!');
        }

        $client->publish("bot:import", 'Процесс завершен!');
        $this->info('Процесс завершен!');
        return 0;
    }
}
