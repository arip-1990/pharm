<?php

namespace App\Http\Controllers\V1\Panel\Product;

use App\Http\Resources\ProductResource;
use App\Product\Entity\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ShowController extends Controller
{
    public function handle(Product $product): JsonResponse
    {
        return new JsonResponse(new ProductResource($product));
    }
}
