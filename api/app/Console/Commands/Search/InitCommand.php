<?php

namespace App\Console\Commands\Search;

use App\Product\Services\Search\ProductIndexer;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class InitCommand extends Command
{
    protected $signature = 'search:init';
    protected $description = 'Initialization search index';

    public function __construct(private readonly ProductIndexer $productIndexer)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $startTime = Carbon::now();
        $redis = Redis::connection('bot')->client();
        try {
            $this->productIndexer->init();

            $redis->publish('bot:search', json_encode([
                'success' => true,
                'type' => 'init',
                'message' => 'Индекс товаров проиницилизировано: ' . $startTime->diff(Carbon::now())->format('%iм %sс')
            ], JSON_UNESCAPED_UNICODE));

            $this->info('Индекс товаров проиницилизировано: ' . $startTime->diff(Carbon::now())->format('%iм %sс'));
            return self::SUCCESS;
        }
        catch (\Exception $e) {
            $redis->publish('bot:search', json_encode([
                'success' => false,
                'type' => 'init',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));

            $this->error($e->getMessage());
            return self::FAILURE;
        }
        finally {
            $startTime = null;
        }
    }
}
