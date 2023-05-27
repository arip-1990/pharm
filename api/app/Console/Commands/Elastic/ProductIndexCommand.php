<?php

namespace App\Console\Commands\Elastic;

use App\Product\Entity\Product;
use App\Store\Entity\Store;
use Carbon\Carbon;
use Illuminate\Console\Command;

use Elasticsearch\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;

class ProductIndexCommand extends Command
{
    protected $signature = 'elastic:index-product';
    protected $description = 'Command description';

    public function __construct(private readonly Client $client)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $config = config('data.elastic.product');
        $startTime = Carbon::now();
        $redis = Redis::connection('bot')->client();
        if (!$this->client->indices()->exists(['index' => $config['index']])) {
            try {
                $this->client->indices()->create([
                    'index' => $config['index'],
                    'body'  => [
                        'settings' => $config['settings'],
                        'mappings' => ['properties' => $config['properties']],
                    ],
                ]);
            }
            catch (\Exception $exception) {
                $this->output->writeln(
                    sprintf(
                        '<error>Error creating index %s, exception message: %s.</error>',
                        $config['index'],
                        $exception->getMessage()
                    )
                );

                return self::FAILURE;
            }
        }

        try {
            Product::has('offers')->chunk(1000, function (Collection $items) use (&$data, $config) {
                /** @var Product $item */
                foreach ($items as $item) {
                    $this->client->index([
                        'id' => $item->id,
                        'index' => $config['index'],
                        'body' => [
                            'id' => $item->id,
                            'slug' => $item->slug,
                            'name' => $item->name,
                            'cities' => Store::active()->select('cities.name')
                                ->whereIn('stores.id', $item->offers()->pluck('store_id'))
                                ->join('locations', 'stores.location_id', '=', 'locations.id')
                                ->join('cities', function ($join) {
                                    $join->on('locations.city_id', '=', 'cities.id')->whereNull('parent_id');
                                })
                                ->groupBy('cities.name')->pluck('name')->toArray(),
                            'values' => $item->values()->whereIn('attribute_id', [1, 2, 3, 5])->pluck('value')->toArray()
                        ]
                    ]);
                }
            });

            $redis->publish('bot:import', json_encode([
                'success' => true,
                'type' => 'category',
                'message' => 'Индексы товаров успешно обновлены: ' . $startTime->diff(Carbon::now())->format('%iм %sс')
            ], JSON_UNESCAPED_UNICODE));
        }
        catch (\Exception $e) {
            $this->redis->publish('bot:import', json_encode([
                'success' => false,
                'type' => 'category',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));

            return self::FAILURE;
        }

        $this->info('Индексы товаров успешно обновлены: ' . $startTime->diff(Carbon::now())->format('%iм %sс'));

        return self::SUCCESS;
    }
}
