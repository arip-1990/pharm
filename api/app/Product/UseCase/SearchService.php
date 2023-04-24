<?php

namespace App\Product\UseCase;

use App\Http\Requests\Catalog\SearchRequest;
use App\Product\Entity\Product;
use Elasticsearch\Client;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Query\Expression;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchService
{
    public function __construct(private readonly Client $client) {}

    public function search(SearchRequest $request, string $city): Paginator
    {
        $page = $request->get('page', 1);
        $pageSize = $request->get('pageSize', 5);

        $response = $this->client->search([
            'index' => config('data.elastic.product.index'),
            'body' => [
                '_source' => ['id', 'name', 'slug'],
                'from' => ($page - 1) * $pageSize,
                'size' => $pageSize,
                'query' => [
                    'bool' => [
                        'must' => [
                            'simple_query_string' => [
                                'query' => $request->get('q'),
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
            return new LengthAwarePaginator([], 0, $pageSize, $page);

        $ids = array_column($data, 'id');
        $items = Product::whereIn('id', $ids)
            ->orderBy(new Expression("position(id::text in '" . implode(',', $ids) . "')"))->get();

        return new LengthAwarePaginator($items, $response['hits']['total']['value'], $pageSize, $page);
    }
}
