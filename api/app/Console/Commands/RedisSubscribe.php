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
                $channel = explode(':', $channel)[1];
                if ($channel === 'import') {
                    $data = json_decode($message, true);
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
