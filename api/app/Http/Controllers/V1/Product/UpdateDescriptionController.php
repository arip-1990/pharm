<?php

namespace App\Http\Controllers\V1\Product;

use App\Models\Product;
use App\Http\Requests\Api\Product\DescriptionRequest;
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
