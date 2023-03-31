<?php

namespace App\Http\Controllers\V1\Panel\Product;

use App\Models\Product;
use App\Http\Requests\Panel\Product\DescriptionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UpdateDescriptionController extends Controller
{
    public function handle(Product $product, DescriptionRequest $request): JsonResponse
    {
        $product->description = $request['description'];

        $product->editor()->associate($request->user());
        $product->save();

        return new JsonResponse(options: JSON_UNESCAPED_UNICODE);
    }
}
