<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Resources\OfferResource;
use App\Http\Resources\ProductResource;
use App\Models\City;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{
    public function handle(Product $product, Request $request): JsonResponse
    {
        return new JsonResponse([
            'product' => new ProductResource($product),
            'offers' => OfferResource::collection(
                $product->offers()->whereCity($request->cookie('city', City::query()->find(1)?->name))->get()
            )
        ]);
    }
}
