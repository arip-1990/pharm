<?php

namespace App\Http\Controllers\V1\Catalog;

use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\City;
use App\Models\Product;
use App\Models\ProductStatistic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PopularController
{
    public function handle(Request $request): JsonResponse
    {
        $popularIds = ProductStatistic::query()->select('id')->orderByDesc('orders')
            ->orderByDesc('views')->get()->pluck('id');

        $categoryIds = Category::query()->whereIn('id', [536, 556])->get()
            ->map(fn(Category $category) => $category->descendants->pluck('id'))->collapse()->push(536, 556);

        $products = Product::active($request->cookie('city', City::query()->find(1)?->name))
            ->whereNotIn('category_id', $categoryIds)->whereIn('id', $popularIds)->take(12)->get();

        return new JsonResponse(ProductResource::collection($products));
    }
}
