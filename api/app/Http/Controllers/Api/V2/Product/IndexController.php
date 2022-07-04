<?php

namespace App\Http\Controllers\Api\V2\Product;

use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Offer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexController
{
    public function handle(Request $request, Category $category = null): JsonResource
    {
        $productIds = Offer::query()->select('product_id')
            ->whereCity($request->get('city', config('data.city')[0]))
            ->groupBy('product_id')->get()->pluck('product_id');
        $query = Product::query()->whereIn('id', $productIds);
        if ($category) {
            $query->whereIn('category_id', $category->descendants()->pluck('id')->push($category->id));
        }

        $products = $query->paginate($request->get('pageSize', 12));

        return ProductResource::collection($products);
    }
}
