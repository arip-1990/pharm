<?php

namespace App\Http\Controllers\V1\Catalog;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\City;
use App\Models\Offer;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndexController
{
    public function handle(Request $request, Category $category = null): JsonResponse
    {
        $productIds = Offer::query()->select('product_id')
            ->whereCity($request->cookie('city', City::query()->find(1)?->name))
            ->groupBy('product_id')->get()->pluck('product_id');
        $query = Product::query()->whereIn('id', $productIds);
        if ($category) {
            $query->whereIn('category_id', $category->descendants()->pluck('id')->push($category->id));
        }

        if ($category)
            $categories = $category->children;
        else
            $categories = Category::query()->whereNull('parent_id')->get();

        $products = $query->paginate($request->get('pageSize', 12));

        return new JsonResponse([
            'categories' => CategoryResource::collection($categories),
            'products' => ProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total()
            ]
        ]);
    }
}
