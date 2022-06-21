<?php

namespace App\Console\Commands;

use App\Models\Offer;
use App\Models\Photo;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

ini_set('memory_limit', -1);

class TestCommand extends Command
{
    protected $signature = 'test';
    protected $description = 'test';

    public function handle(): int
    {
//        $productIds = Offer::query()->select('product_id')->groupBy('product_id')->get()->pluck('product_id');
//        /** @var Product $product */
//        foreach (Product::query()->findMany($productIds) as $product) {
//            $product->photos()->update(['status' => Photo::STATUS_CHECKED]);
//        }
        echo PHP_EOL;
        $this->info(PHP_EOL . 'Очистка успешно завершена!');
        return 0;
    }
}
