<?php

namespace App\Console\Commands\Import;

use App\Product\UseCase\CategoryService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class CategoryCommand extends Command
{
    protected $signature = 'import:category';
    protected $description = 'Import categories';

    public function handle(CategoryService $service): int
    {
        $startTime = Carbon::now();
        $redis = Redis::connection('bot')->client();

        try {
            $service->updateData();

            $redis->publish('bot:import', json_encode([
                'success' => true,
                'type' => 'category',
                'message' => 'Категории успешно обновлены: ' . $startTime->diff(Carbon::now())->format('%iм %sс')
            ], JSON_UNESCAPED_UNICODE));
        } catch (\DomainException $e) {
            $redis->publish('bot:import', json_encode([
                'success' => false,
                'type' => 'category',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));

            return self::FAILURE;
        } catch (\RedisException $e) {
            return self::FAILURE;
        } finally {
            $startTime = null;
        }

        return self::SUCCESS;
    }
}
