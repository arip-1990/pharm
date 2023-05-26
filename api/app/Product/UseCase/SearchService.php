<?php

namespace App\Product\UseCase;

use Elasticsearch\Client;

readonly class SearchService
{
    public function __construct(private Client $client) {}

    public function search(string $text, int $from = 0, int $limit = 0, string $city = null): array
    {
        $response = $this->client->search($this->generateQuery($text, $from * $limit, $limit, $city));

        return array_map(fn(array $item) => [
            ...$item['_source'],
            'highlight' => $item['highlight']['name'][0] ?? null,
        ], $response['hits']['hits']);
    }

    protected function generateQuery(string $text, int $from = 0, int $size = 0, string $city = null): array
    {
        $queryString = ['simple_query_string' => ['query' => $text, 'fields' => ['name^2', 'values']]];
        $query = [
            'index' => config('data.elastic.product.index'),
            'body' => [
                '_source' => ['id', 'name', 'slug'],
                'query' => $city ? [
                    'bool' => [
                        'must' => $queryString,
                        'filter' => ['term' => ['cities' => $city]]
                    ]
                ] : $queryString,
                'highlight' => ['fields' => ['name' => new \stdClass()]],
            ]
        ];

        if ($size) {
            $query['body']['from'] = $from;
            $query['body']['size'] = $size;
        }

        return $query;
    }
}
