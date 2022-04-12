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
        $message = '';
        Redis::subscribe(['update'], function (string $type) use (&$message) {
            $code = 1;
            switch ($type) {
                case 'category':
                    $code = Artisan::call('import:category');
                    break;
                case 'offer':
                    $code = Artisan::call('import:offer');
                    break;
                case 'product':
                    $code = Artisan::call('import:product');
                    break;
                case 'store':
                    $code = Artisan::call('import:store');
            }

            $message = $code ? 'Произошла ошибка при обновлении' : $type . ' успешно обновлено';
        });

        Redis::publish('bot', $message);

        return 0;
    }
}
