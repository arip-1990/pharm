<?php

namespace App\Http\Controllers\V2\Product;

use App\Http\Resources\ProductResource;
use App\Product\Entity\Category;
use App\Product\Entity\Product;
use App\Store\Entity\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class PopularsController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $data = Cache::remember('popular_products', 3600, function () use ($request) {
            $categoryIds = Category::with('descendants')->whereIn('id', [536, 556])->get()
                ->map(fn(Category $category) => $category->descendants->pluck('id'))->collapse()->push(536, 556);

            return Product::active($request->cookie('city', City::query()->find(1)?->name))->select('products.*')
                ->whereNotIn('products.category_id', $categoryIds)
                ->join('product_statistics', 'product_statistics.id', '=', 'products.id')
                ->orderByDesc('product_statistics.show')->orderByRaw('(product_statistics.orders + product_statistics.views) desc')
                ->take(16)->get();
        });

        return new JsonResponse(ProductResource::collection($data));
    }
}
