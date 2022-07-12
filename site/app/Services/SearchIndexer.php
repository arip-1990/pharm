<?php

namespace App\Services;

use App\Models\Attribute;
use App\Models\Product;
use App\Models\Value;
use Elasticsearch\Client;
use Illuminate\Database\Eloquent\Builder;

class SearchIndexer
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
                'description' => $product->description,
                'categories' => $product->category ? array_merge(
                    [$product->category->id],
                    $product->category->ancestors()->pluck('id')->toArray()
                ) : [],
                'values' => array_map(function (Value $value) {
                    return [
                        'attribute' => $value->attribute_id,
                        'value' => (string)$value->value,
                    ];
                }, $product->values()->whereHas('attribute', function (Builder $query) {
                    $query->where('type', '!=', Attribute::TYPE_TEXT);
                })->getModels()),
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
