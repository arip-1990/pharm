<?php

namespace App\Console\Commands\Elastic;

use App\Models\Store;
use App\Product\Entity\Product;
use Illuminate\Console\Command;

use Elasticsearch\Client;
use Illuminate\Support\Collection;

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
            $this->client->bulk([
                'index' => $config['index'],
                'body'  => $this->generateData($config['index']),
            ]);
        }
        catch (\Exception $exception) {
            $this->output->writeln(
                sprintf(
                    '<error>Error updating mapping for index %s, error message: %s.</error>',
                    $config['index'],
                    $exception->getMessage()
                )
            );

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function generateData(string $index): array
    {
        $data = [];
        Product::has('offers')->chunk(1000, function (Collection $items) use (&$data, $index) {
            /** @var Product $item */
            foreach ($items as $item) {
                $data[] = [
                    'index' => ['_index' => $index],
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
                    'values' => $item->values()->whereIn('attribute_id', [1, 3, 5])->pluck('value')->toArray()
                ];
            }
        });

        return $data;
    }
}
