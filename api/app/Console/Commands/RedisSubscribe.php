<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redis;

class RedisSubscribe extends Command
{
    protected $signature = 'redis:subscribe';
    protected $description = 'Subscribe to a Redis channel';

    public function handle(): int
    {
        $client = Redis::connection('bot')->client();
        Redis::subscribe(['update'], function (string $data) use ($client) {
            $data = json_decode($data, true);
            $message = 'Данные успешно обновлены';
            switch ($data['type']) {
                case 'category':
                    if (Artisan::call('import:category'))
                        $message = 'Ошибка обновления данных';
                    break;
                case 'offer':
                    if (Artisan::call('import:offer'))
                        $message = 'Ошибка обновления данных';
                    break;
                case 'product':
                    if (Artisan::call('import:product'))
                        $message = 'Ошибка обновления данных';
                    break;
                case 'store':
                    if (Artisan::call('import:store'))
                        $message = 'Ошибка обновления данных';
                    break;
                case 'test':
                    if (Artisan::call('order', ['orderId' => $data['orderId']]))
                        $message = 'Ошибка данных';
            }

            $client->publish('bot:import', json_encode(['success' => true, 'message' => $message]));
        });

        return 0;
    }
}
