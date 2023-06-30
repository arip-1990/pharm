<?php

namespace App\Console\Commands\Import;

use App\Store\UseCase\StoreService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class StoreCommand extends Command
{
    protected $signature = 'import:store';
    protected $description = 'Import pharmacies';

    public function handle(StoreService $service): int
    {
        $startTime = Carbon::now();
        $redis = Redis::connection('bot')->client();

        try {
            $service->updateData();

            $redis->publish('bot:import', json_encode([
                'success' => true,
                'type' => 'store',
                'message' => 'Аптеки успешно обновлены: ' . $startTime->diff(Carbon::now())->format('%iм %sс')
            ], JSON_UNESCAPED_UNICODE));
        } catch (\DomainException $e) {
            $redis->publish('bot:import', json_encode([
                'success' => false,
                'type' => 'product',
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
