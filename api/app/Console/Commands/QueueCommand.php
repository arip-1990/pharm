<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class QueueCommand extends Command
{
    protected $signature = 'queue:list {connection=default}';
    protected $description = 'Queues list';

    public function handle(): int
    {
        foreach (Redis::connection($this->argument('connection'))->command('lrange', ['queues:default', 0, -1]) as $item) {
            print_r(json_decode($item, true));
            echo PHP_EOL;
        }

        return 0;
    }
}