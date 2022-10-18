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
        Redis::subscribe(['update'], function (string $data) {
            $data = json_decode($data, true);
            switch ($data['type']) {
                case 'category': Artisan::call('import:category'); break;
                case 'offer': Artisan::call('import:offer'); break;
                case 'product': Artisan::call('import:product'); break;
                case 'store': Artisan::call('import:store'); break;
                case 'test': Artisan::call('order', ['orderId' => $data['orderId']]);
            }
        });

        return 0;
    }
}
