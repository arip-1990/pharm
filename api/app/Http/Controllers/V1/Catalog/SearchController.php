<?php

namespace App\Http\Controllers\V1\Catalog;

use App\Http\Resources\ProductResource;
use App\Models\City;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchController
{
    public function handle(Request $request): JsonResponse | JsonResource
    {
        if (!$searchText = $request->get('q')) {
            return new JsonResponse([
                'code' => 0,
                'message' => 'Введите запрос для поиска'
            ], 500);
        }

        $paginator = Product::active($request->cookie('city', City::query()->find(1)?->name))
            ->where(function(Builder $query) use ($searchText) {
                $query->where('name', 'like', '%' . $searchText . '%')
                    ->orWhereRaw('to_tsvector(name) @@ plainto_tsquery(?)', [$searchText]);
            })->paginate(30);

        return ProductResource::collection($paginator);
    }
}
