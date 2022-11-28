<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

ini_set('memory_limit', -1);

class TestCommand extends Command
{
    protected $signature = 'test';
    protected $description = 'test';

    public function handle(): int
    {
        echo Product::where('code', 49322)->first()->id . PHP_EOL;
        $this->info("Процесс завершена!");
        return 0;
    }
}
