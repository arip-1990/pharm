<?php

namespace App\Http\Controllers\V1\Catalog;

use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\City;
use App\Models\Product;
use App\Models\ProductStatistic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PopularController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $categoryIds = Category::whereIn('id', [536, 556])->get()
            ->map(fn(Category $category) => $category->descendants()->pluck('id'))->collapse()->push(536, 556);

        $popularIds = ProductStatistic::select('id')->orderByDesc('orders')
            ->orderByDesc('views')->get()->pluck('id');

        $products = Product::active($request->cookie('city', City::query()->find(1)?->name))
            ->whereNotIn('category_id', $categoryIds)->whereIn('id', $popularIds)->take(16)->get();

        return new JsonResponse(ProductResource::collection($products));
    }
}
