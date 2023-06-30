<?php

namespace App\Console\Commands\Import;

use App\Product\UseCase\OfferService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class OfferCommand extends Command
{
    protected $signature = 'import:offer {type=all}';
    protected $description = 'Import offers
                            {change : update offers}
                            {stock : import stock offers}';

    public function handle(OfferService $service): int
    {
        $type = $this->argument('type');
        $startTime = Carbon::now();
        $redis = Redis::connection('bot')->client();

        try {
            $service->updateData($type);

            if ($type === 'all') {
                $redis->publish('bot:import', json_encode([
                    'success' => true,
                    'type' => 'offer:' . $type,
                    'message' => 'Остатки успешно обновлены: ' . $startTime->diff(Carbon::now())->format('%iм %sс')
                ], JSON_UNESCAPED_UNICODE));
            }
        } catch (\DomainException $e) {
            $redis->publish('bot:import', json_encode([
                'success' => false,
                'type' => 'offer:' . $type,
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
