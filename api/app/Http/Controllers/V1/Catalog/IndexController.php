<?php

namespace App\Http\Controllers\V1\Catalog;

use App\Http\Resources\{CategoryResource, ProductResource};
use App\Models\City;
use App\Product\Entity\{Category, Offer, Product};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(Request $request, Category $category = null): JsonResponse
    {
        $productIds = Offer::select('product_id')->whereCity($request->cookie('city', City::find(1)?->name))
            ->groupBy('product_id')->pluck('product_id');

        $query = Product::whereIn('id', $productIds);
        if ($category) {
            $categories = $category->children;
            $query->whereIn('category_id', $category->descendants()->pluck('id')->push($category->id));
        }
        else
            $categories = Category::whereNull('parent_id')->get();

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
