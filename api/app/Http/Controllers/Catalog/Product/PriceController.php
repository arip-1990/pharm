<?php

namespace App\Http\Controllers\Catalog\Product;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class PriceController extends Controller
{
    public function handle(Product $product): JsonResponse
    {
        return new JsonResponse($product->getPrice());
    }
}
