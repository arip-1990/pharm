<?php

namespace App\Console\Commands\Search;

use App\Models\Product;
use App\Services\SearchIndexer;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ReindexCommand extends Command
{
    protected $signature = 'search:reindex';

    public function __construct(private SearchIndexer $products)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $startTime = Carbon::now();

        $this->products->clear();

        Product::active()->chunk(1000, function ($products) {
           foreach ($products as $product) {
               $this->products->index($product);
           }
        });

        $this->info('Переиндексация завершена! ' . $startTime->diff(Carbon::now())->format('%mм %sс'));
        return 0;
    }
}
