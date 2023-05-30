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
                        switch ($data['type']) {
                            case 'category':
                                Artisan::call('import:category');
                                break;
                            case 'product':
                                Artisan::call('import:product');
                                break;
                            case 'store':
                                Artisan::call('import:store');
                                break;
                            case 'offer':
                                Artisan::call('import:offer');
                                break;
                            default:
                                throw new \InvalidArgumentException('Неверная комманда!');
                        }
                        break;
                    case 'search':
                        if ($data['type'] === 'init') Artisan::call('search:init');
                        elseif ($data['type'] === 'reindex') Artisan::call('search:reindex');
                        else throw new \InvalidArgumentException('Неверная комманда!');
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
