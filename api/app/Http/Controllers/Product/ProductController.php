<?php

namespace App\Http\Controllers\Product;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{
    public function handle(Product $product): JsonResponse
    {
        return new JsonResponse(new ProductResource($product));
    }
}
