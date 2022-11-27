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
use Illuminate\Routing\Controller;

class StockController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $productIds = Offer::select('product_id')->whereCity($request->cookie('city', City::find(1)?->name))
            ->groupBy('product_id')->pluck('product_id');

        $products = Product::whereIn('id', $productIds)->whereNotNull('discount_id')
            ->paginate($request->get('pageSize', 20));

        return new JsonResponse([
            'categories' => CategoryResource::collection(Category::whereNull('parent_id')->get()),
            'products' => ProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total()
            ]
        ]);
    }
}
