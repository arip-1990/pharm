<?php

namespace App\Http\Controllers\V1\Panel\Product;

use App\Http\Requests\Panel\Product\DescriptionRequest;
use App\Product\Entity\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UpdateDescriptionController extends Controller
{
    public function handle(Product $product, DescriptionRequest $request): JsonResponse
    {
        $product->update(['description' => $request['description']]);

        return response()->json();
    }
}
