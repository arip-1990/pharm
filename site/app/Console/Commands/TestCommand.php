<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

ini_set('memory_limit', -1);

class TestCommand extends Command
{
    protected $signature = 'test {file}';
    protected $description = 'test';

    public function handle(): int
    {
    //    $total = Product::withTrashed()->count();
    //    $i = 1;
    //    Product::withTrashed()->chunk(1000, function ($products) use ($total, &$id, &$i) {
    //        /** @var Product $product */
    //        foreach ($products as $product) {
    //            echo "\033[2KProcessing: " . $i . '/' . $total . "\r";

    //            if ($product->isPrescription())
    //                $product->update(['recipe' => true]);

    //            $i++;
    //        }
    //    });

    //     $this->info(PHP_EOL . 'Обновление успешно завершена!');
        return 0;
    }
}
