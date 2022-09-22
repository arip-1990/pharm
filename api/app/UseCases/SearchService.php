<?php

namespace App\UseCases;

use App\Http\Requests\Catalog\SearchRequest;
use App\Models\Product;
use Elasticsearch\Client;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Query\Expression;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchService
{
    public function __construct(private readonly Client $client) {}

    public function search(SearchRequest $request, string $city, int $perPage): Paginator
    {
        $response = $this->client->search([
            'index' => 'products',
            'body' => [
                '_source' => ['id'],
                'from' => ($request->get('page', 1) - 1) * $perPage,
                'size' => $perPage,
                'query' => [
                    'bool' => [
                        'must' => array_merge(
                            array_filter([!empty($request->get('q')) ? ['match' => ['name' => $request->get('q')]] : false]),
//                            array_map(function ($id) use ($request) {
//                                return [
//                                    'nested' => [
//                                        'path' => 'values',
//                                        'query' => [
//                                            'bool' => [
//                                                'should' => array_values(array_filter([
//                                                    ['match' => ['values.attribute' => $id]],
//                                                    !empty($request->get('q')) ? ['match' => ['values.value' => $request->get('q')]] : false,
//                                                ])),
//                                            ],
//                                        ],
//                                    ],
//                                ];
//                            }, [1,3])
                        )
                    ],
                ],
            ],
        ]);

        $ids = array_column($response['hits']['hits'], '_id');

        if ($ids) {
            $items = Product::active($city)->whereIn('id', $ids)
                ->orderBy(new Expression("position(id::text in '" . implode(',', $ids) . "')"))->get();
            $pagination = new LengthAwarePaginator($items, $response['hits']['total']['value'], $perPage, $request->get('page', 1), ['path' => route('catalog.search')]);
        }
        else {
            $pagination = new LengthAwarePaginator([], 0, $perPage, $request->get('page', 1));
        }

        return $pagination;
    }
}
