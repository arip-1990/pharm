<?php

namespace App\Http\Controllers\V1\Catalog;

use App\Http\Requests\Catalog\SearchRequest;
use App\Http\Resources\ProductResource;
use App\Product\Entity\Product;
use App\Product\UseCase\SearchService;
use App\Store\Entity\City;
use Illuminate\Database\Query\Expression;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;

class SearchController extends Controller
{
    public function __construct(private readonly SearchService $searchService) {}

    public function handle(SearchRequest $request): JsonResponse | JsonResource
    {
        if (!$text = $request->get('q')) {
            return new JsonResponse([
                'code' => 0,
                'message' => 'Введите запрос для поиска'
            ], 500);
        }

        if ($request->get('full')) {
            $data = $this->searchService->search($text, city: $request->cookie('city', City::find(1)?->name));

            $ids = array_column($data, 'id');
            $data = Product::whereIn('id', $ids)->orderBy(new Expression("position(id::text in '" . implode(',', $ids) . "')"))
                ->paginate($request->get('pageSize', 10));

            return ProductResource::collection($data);
        }

        $tmp = [];
        $data = $this->searchService->search($text, limit: 5, city: $request->cookie('city', City::find(1)?->name));

        return new JsonResponse(array_values(array_filter($data, function ($item) use (&$tmp) {
            if (!in_array($item['id'], $tmp)) {
                $tmp[] = $item['id'];
                return true;
            }

            return false;
        })));
    }
}
