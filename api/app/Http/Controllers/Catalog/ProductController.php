<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Resources\OfferResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{
    public function handle(Product $product): JsonResponse
    {
        return new JsonResponse([
            'product' => new ProductResource($product),
            'offers' => OfferResource::collection($product->offers)
        ]);
    }
}
