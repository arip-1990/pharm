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
        $client = Redis::connection()->client();
        Redis::connection('subscribe')->subscribe(['update'], function (string $data) use ($client) {
            $message = '';
            $type = 'update';
            try {
                $data = json_decode($data, true);
                $client->publish("bot:test", json_encode(['chatId' => $data['chatId'], 'message' => 'Начинаем выполнение запроса...']));
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
                        $type = 'test';
                        $code = Artisan::call('send:order', ['order' => $data['order']]);
                        $message = $code ? 'Произошла ошибка при обработке запроса' : 'Запрос обработан успешно';
                }
            }
            catch (\Exception $exception) {
                $message = $exception->getMessage();
            }

            $client->publish("bot:{$type}", json_encode(['chatId' => $data['chatId'], 'message' => $message]));
        });

        return 0;
    }
}
