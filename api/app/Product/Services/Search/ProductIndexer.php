<?php

namespace App\Product\Services\Search;

use App\Product\Entity\Product;
use App\Store\Entity\Store;
use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;

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

    public function init(): void
    {
        try {
            $this->client->indices()->delete(['index' => 'products']);
        }
        catch (Missing404Exception $e) {}

        $this->client->indices()->create([
            'index' => 'products',
            'body' => [
                'mappings' => [
                    'properties' => [
                        'id' => ['type' => 'keyword'],
                        'name' => ['type' => 'text'],
                        'slug' => ['type' => 'keyword'],
                        'code' => ['type' => 'integer'],
                        'description' => ['type' => 'text'],
                        'categories' => ['type' => 'integer'],
                        'values' => ['type' => 'text'],
                        'cities' => ['type' => 'keyword', 'normalizer' => 'lowercase'],
                    ],
                ],
                'settings' => [
                    'analysis' => [
                        'char_filter' => [
                            'replace' => [
                                'type' => 'mapping',
                                'mappings' => ['&=> and '],
                            ],
                        ],
                        'filter' => [
                            'ru_stop' => [
                                'type' => 'stop',
                                'stopwords' => '_russian_',
                            ],
                            'ru_stemmer' => [
                                'type' => 'stemmer',
                                'language' => 'russian',
                            ],
                            'word_delimiter' => [
                                'type' => 'word_delimiter',
                                'split_on_numerics' => false,
                                'split_on_case_change' => true,
                                'generate_word_parts' => true,
                                'generate_number_parts' => true,
                                'catenate_all' => true,
                                'preserve_original' => true,
                                'catenate_numbers' => true,
                            ],
                        ],
                        'analyzer' => [
                            'default' => [
                                'type' => 'custom',
                                'char_filter' => ['html_strip', 'replace'],
                                'tokenizer' => 'whitespace',
                                'filter' => [
                                    'lowercase',
                                    'word_delimiter',
                                    'ru_stop',
                                    'ru_stemmer',
                                ],
                            ],
                        ],
                    ],
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
