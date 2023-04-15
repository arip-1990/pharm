<?php

namespace App\Http\Controllers\V1\Catalog;

use App\Http\Resources\{CategoryResource, ProductResource};
use App\Models\City;
use App\Product\Entity\{Category, Product};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class StockController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $products = Product::active($request->cookie('city', City::find(1)?->name))->has('discounts')
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
