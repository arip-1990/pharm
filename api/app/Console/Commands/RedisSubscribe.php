<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redis;
use Predis\Client;

class RedisSubscribe extends Command
{
    protected $signature = 'redis:subscribe';
    protected $description = 'Subscribe to a Redis channel';

    public function handle(): int
    {
        $redis = new Client('tcp://' . env('REDIS_HOST', '127.0.0.1') . ':' . env('REDIS_PORT', '6379'));
        Redis::subscribe(['update'], function (string $data) use ($redis) {
            $message = '';
            try {
                $data = json_decode($data, true);
                switch ($data['type']) {
                    case 'category':
                        $code = Artisan::call('import:category');
                        $message = $code ? 'Произошла ошибка при обновлении категории' : 'Категории успешно обновлены';
                        break;
                    case 'offer':
                        $code = Artisan::call('import:offer');
                        $message = $code ? 'Произошла ошибка при обновлении остатков' : 'Остатки успешно обновлены';
                        break;
                    case 'product':
                        $code = Artisan::call('import:product');
                        $message = $code ? 'Произошла ошибка при обновлении товаров' : 'Товары успешно обновлены';
                        break;
                    case 'store':
                        $code = Artisan::call('import:store');
                        $message = $code ? 'Произошла ошибка при обновлении аптек' : 'Аптеки успешно обновлены';
                        break;
                    case 'test':
                        $code = Artisan::call('test');
                        $message = $code ? 'Произошла ошибка при обработке запроса' : 'Запрос обработан успешно';
                }
            }
            catch (\Exception $exception) {
                $message = $exception->getMessage();
            }

            $redis->publish('bot:update', json_encode(['chatId' => $data['chatId'], 'message' => $message]));
        });

        return 0;
    }
}
