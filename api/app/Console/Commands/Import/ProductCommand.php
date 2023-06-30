<?php

namespace App\Console\Commands\Import;

use App\Product\UseCase\ProductService;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProductCommand extends Command
{
    protected $signature = 'import:product';
    protected $description = 'Import products';

    public function handle(ProductService $service): int
    {
        $startTime = Carbon::now();
        $redis = Redis::connection('bot')->client();

        try {
            $service->updateData();

            $redis->publish('bot:import', json_encode([
                'success' => true,
                'type' => 'product',
                'message' => 'Товары успешно обновлены: ' . $startTime->diff(Carbon::now())->format('%iм %sс')
            ], JSON_UNESCAPED_UNICODE));
        } catch (\DomainException $e) {
            $redis->publish('bot:import', json_encode([
                'success' => false,
                'type' => 'product',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));

            return self::FAILURE;
        } catch (\RedisException $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        } finally {
            $startTime = null;
        }

        return self::SUCCESS;
    }
}
