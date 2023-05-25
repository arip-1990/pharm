<?php

namespace App\Product\UseCase;

use Elasticsearch\Client;

readonly class SearchService
{
    public function __construct(private Client $client) {}

    public function search(string $text, int $from = 0, int $limit = 10, string $city = null): array
    {
        $response = $this->client->search($this->generateQuery($text, $from * $limit, $limit, $city));

        return array_map(fn(array $item) => [
            ...$item['_source'],
            'score' => $item['_score'],
            'highlight' => $item['highlight']['name'][0] ?? null,
        ], $response['hits']['hits']);
    }

    private function generateQuery(string $text, int $from = 0, int $size = 10, string $city = null): array
    {
        $queryString = ['simple_query_string' => ['query' => $text, 'fields' => ['name^2', 'values']]];
        return [
            'index' => config('data.elastic.product.index'),
            'body' => [
                '_source' => ['id', 'name', 'slug'],
//                'from' => $from,
//                'size' => $size,
                'query' => $city ? [
                    'bool' => [
                        'must' => $queryString,
                        'filter' => ['term' => ['cities' => $city]]
                    ]
                ] : $queryString,
                'highlight' => ['fields' => ['name' => new \stdClass()]],
            ]
        ];
    }
}
