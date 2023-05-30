<?php

namespace App\Product\Services\Search;

use App\Product\Entity\Product;
use App\Store\Entity\Store;
use Elasticsearch\Client;

readonly class ProductIndexer
{
    public function __construct(private Client $client) {}

    public function clear(): void
    {
        $this->client->deleteByQuery([
            'index' => 'products',
            'body' => [
                'query' => [
                    'match_all' => new \stdClass(),
                ],
            ],
        ]);
    }

    public function index(Product $product): void
    {
        $this->client->index([
            'index' => 'products',
            'id' => $product->id,
            'body' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'code' => $product->code,
                'description' => $product->description,
                'categories' => $product->category ? array_merge([$product->category->id], $product->category->ancestors()->pluck('id')->toArray()) : [0],
                'values' => $product->values()->whereIn('attribute_id', [1, 2, 3, 5])->pluck('value')->toArray(),
                'cities' => Store::active()->select('cities.name')
                    ->whereIn('stores.id', $product->offers()->pluck('store_id'))
                    ->join('locations', 'stores.location_id', '=', 'locations.id')
                    ->join('cities', function ($join) {
                        $join->on('locations.city_id', '=', 'cities.id')->whereNull('parent_id');
                    })
                    ->groupBy('cities.name')->pluck('name')->toArray(),
            ],
        ]);
    }

    public function remove(Product $product): void
    {
        $this->client->delete([
            'index' => 'products',
            'id' => $product->id,
        ]);
    }
}
