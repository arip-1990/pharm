<?php

namespace App\Http\Controllers\V1\Catalog;

use App\Http\Requests\Catalog\SearchRequest;
use App\Http\Resources\ProductResource;
use App\Models\City;
use App\Product\UseCase\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;

class SearchController extends Controller
{
    public function __construct(private readonly SearchService $searchService) {}

    public function handle(SearchRequest $request): JsonResponse | JsonResource
    {
        if (!$request->get('q')) {
            return new JsonResponse([
                'code' => 0,
                'message' => 'Введите запрос для поиска'
            ], 500);
        }

        $paginator = $this->searchService->search($request, $request->cookie('city', City::find(1)?->name));

        return ProductResource::collection($paginator);
    }
}
