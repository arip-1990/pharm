<?php

namespace App\Http\Controllers\V1\Panel\Offer;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ShowController extends Controller
{
    public function handle(Product $product): JsonResponse
    {
        return new JsonResponse(new ProductResource($product));
    }
}
