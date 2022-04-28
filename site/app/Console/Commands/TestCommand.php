<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

ini_set('memory_limit', -1);

class TestCommand extends Command
{
    protected $signature = 'test';
    protected $description = 'test';

    public function handle(): int
    {
        $this->info(PHP_EOL . 'Очистка успешно завершена!');
        return 0;
    }
}
