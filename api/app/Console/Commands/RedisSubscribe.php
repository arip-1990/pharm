<?php

namespace App\Console\Commands;

use App\Product\Entity\Product;
use App\Product\Services\Search\ProductIndexer;
use App\Product\UseCase\CategoryService;
use App\Product\UseCase\OfferService;
use App\Product\UseCase\ProductService;
use App\Store\UseCase\StoreService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redis;

class RedisSubscribe extends Command
{
    protected $signature = 'redis:subscribe';
    protected $description = 'Subscribe to a Redis channel';

    public function handle(): void
    {
        $client = Redis::connection('bot')->client();
        Redis::psubscribe(['api:*'], function (string $message, string $channel) use ($client) {
            try {
                $data = json_decode($message, true);
                switch (explode(':', $channel)[1]) {
                    case 'import':
                        if (in_array($data['type'], ['category', 'product', 'store', 'offer']))
                            $this->importData($data['type']);
                        else
                            throw new \InvalidArgumentException('Неверная комманда для обновления данных!');
                        break;
                    case 'search':
                        if (in_array($data['type'], ['init', 'reindex']))
                            $this->searchData($data['type']);
                        else
                            throw new \InvalidArgumentException('Неверная комманда для индексирования поиска!');
                        break;
                    case 'send':
                        Artisan::call('order:send' . ($data['date'] ?? ''));
                        break;
                    default:
                        throw new \InvalidArgumentException('Неверная комманда!');
                }
            }
            catch (\Exception $e) {
                $client->publish('bot:error', json_encode([
                    'file' => self::class . '(' . $e->getLine() . ')',
                    'message' => $e->getMessage()
                ], JSON_UNESCAPED_UNICODE));
            }
        });
    }

    private function importData(string $type): void
    {
        $startTime = Carbon::now();
        $redis = Redis::connection('bot')->client();

        try {
            $service = null;
            switch ($type) {
                case 'category':
                    $service = new CategoryService();
                    break;
                case 'product':
                    $service = new ProductService();
                    break;
                case 'store':
                    $service = new StoreService();
                    break;
                case 'offer':
                    $service = new OfferService();
            }

            $service?->updateData();

            $redis->publish('bot:import', json_encode([
                'success' => true,
                'type' => $type,
                'message' => 'Данные обновлены: ' . $startTime->diff(Carbon::now())->format('%iм %sс')
            ], JSON_UNESCAPED_UNICODE));
        } catch (\Exception $e) {
            $redis->publish('bot:import', json_encode([
                'success' => false,
                'type' => $type,
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
        } finally {
            $startTime = null;
        }
    }

    private function searchData(string $type): void
    {
        $startTime = Carbon::now();
        $redis = Redis::connection('bot')->client();

        try {
            /** @var ProductIndexer $service */
            $service = App::make(ProductIndexer::class);

            if ($type === 'init') {
                $service?->init();
            }
            else {
                $service?->clear();

                foreach (Product::has('offers')->cursor() as $product) {
                    $service?->index($product);
                }
            }

            $redis->publish('bot:search', json_encode([
                'success' => true,
                'type' => $type,
                'message' => 'Индекс обновлен: ' . $startTime->diff(Carbon::now())->format('%iм %sс')
            ], JSON_UNESCAPED_UNICODE));
        } catch (\Exception $e) {
            $redis->publish('bot:search', json_encode([
                'success' => false,
                'type' => $type,
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
        } finally {
            $startTime = null;
        }
    }
}
