<?php

namespace App\Console\Commands\Search;

use App\Product\Entity\Product;
use App\Product\Services\Search\ProductIndexer;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class ReindexCommand extends Command
{
    protected $signature = 'search:reindex';

    public function __construct(private readonly ProductIndexer $productIndexer)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $startTime = Carbon::now();
        $redis = Redis::connection('bot')->client();
        try {
            $this->productIndexer->clear();

            foreach (Product::has('offers')->cursor() as $product) {
                $this->productIndexer->index($product);
            }

            $redis->publish('bot:search', json_encode([
                'success' => true,
                'type' => 'reindex',
                'message' => 'Индексы товаров успешно обновлены: ' . $startTime->diff(Carbon::now())->format('%iм %sс')
            ], JSON_UNESCAPED_UNICODE));

            return self::SUCCESS;
        }
        catch (\Exception $e) {
            $redis->publish('bot:search', json_encode([
                'success' => false,
                'type' => 'reindex',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));

            return self::FAILURE;
        }
        finally {
            $startTime = null;
        }
    }
}
