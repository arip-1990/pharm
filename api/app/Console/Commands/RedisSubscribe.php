<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redis;

class RedisSubscribe extends Command
{
    protected $signature = 'redis:subscribe';
    protected $description = 'Subscribe to a Redis channel';

    public function handle(): void
    {
        $client = Redis::connection('bot')->client();
        Redis::psubscribe(['api:*'], function (string $message, string $channel) use ($client) {
            try {
                $data = json_decode($message, true);
                switch (explode(':', $channel)[1]) {
                    case 'import':
                        if (in_array($data['type'], ['category', 'product', 'store', 'offer']))
                            Artisan::call("import:{$data['type']}");
                        else
                            throw new \InvalidArgumentException('Неверная комманда для обновления данных!');
                        break;
                    case 'search':
                        if (in_array($data['type'], ['init', 'reindex']))
                            Artisan::call("search:{$data['type']}");
                        else
                            throw new \InvalidArgumentException('Неверная комманда для индексирования поиска!');
                        break;
                    case 'send':
                        Artisan::call('order:send' . ($data['date'] ?? ''));
                        break;
                    default:
                        throw new \InvalidArgumentException('Неверная комманда!');
                }
            }
            catch (\Exception $e) {
                $client->publish('bot:error', json_encode([
                    'file' => self::class . '(' . $e->getLine() . ')',
                    'message' => $e->getMessage()
                ], JSON_UNESCAPED_UNICODE));
            }
        });
    }
}
