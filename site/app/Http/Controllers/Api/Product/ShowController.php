<?php

namespace App\Http\Controllers\Api\Product;

use App\Entities\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ShowController extends Controller
{
    public function handle(Product $product): JsonResponse
    {
        return response()->json($product);
    }
}
