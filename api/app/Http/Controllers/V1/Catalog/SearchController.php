<?php

namespace App\Http\Controllers\V1\Catalog;

use App\Http\Requests\Catalog\SearchRequest;
use App\Http\Resources\ProductResource;
use App\Product\UseCase\SearchService;
use App\Store\Entity\City;
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

        $paginator = $this->searchService->searchByCity(
            $text,
            $request->cookie('city', City::find(1)?->name),
            $request->get('page', 1) - 1,
            $request->get('pageSize', 10)
        );

        return ProductResource::collection($paginator);
    }
}
