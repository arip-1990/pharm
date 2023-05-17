<?php

namespace App\Product\UseCase;

use App\Product\Entity\Product;
use Elasticsearch\Client;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Query\Expression;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchService
{
    public function __construct(private readonly Client $client) {}

    public function search(string $text, int $from = 0, int $limit = 10): array
    {
        $response = $this->client->search([
            'index' => config('data.elastic.product.index'),
            'body' => [
                '_source' => ['id', 'name', 'slug'],
                'from' => $from * $limit,
                'size' => $limit,
                'query' => [
                    'simple_query_string' => [
                        'query' => $text,
                        'fields' => ['name^3', 'values'],
                    ]
                ],
                'highlight' => ['fields' => ['name' => new \stdClass()]],
            ],
        ]);

        $data = array_map(fn(array $item) => [
            ...$item['_source'],
            'highlight' => $item['highlight']['name'][0] ?? null,
        ], $response['hits']['hits']);

        return array_column($data, 'id');
    }

    public function searchByCity(string $text, string $city, int $from = 0, int $limit = 10): Paginator
    {
        $response = $this->client->search([
            'index' => config('data.elastic.product.index'),
            'body' => [
                '_source' => ['id', 'name', 'slug'],
                'from' => $from * $limit,
                'size' => $limit,
                'query' => [
                    'bool' => [
                        'must' => [
                            'simple_query_string' => [
                                'query' => $text,
                                'fields' => ['name^3', 'values'],
                            ]
                        ],
                        'filter' => ['term' => ['cities' => $city]],
                    ],
                ],
                'highlight' => ['fields' => ['name' => new \stdClass()]],
            ],
        ]);

        $data = array_map(fn(array $item) => [
            ...$item['_source'],
            'highlight' => $item['highlight']['name'][0] ?? null,
        ], $response['hits']['hits']);

        if (!count($data))
            return new LengthAwarePaginator([], 0, $limit, $from + 1);

        $ids = array_column($data, 'id');
        return Product::whereIn('id', $ids)->orderBy(new Expression("position(id::text in '" . implode(',', $ids) . "')"))
            ->paginate($limit, page: $from + 1);

//        return new LengthAwarePaginator($items, $response['hits']['total']['value'], $limit, $from + 1);
    }
}
