<?php

namespace App\Http\Controllers\V1\Panel\Product\Moderation;

use App\Product\Entity\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateController
{
    public function handle(Product $product, Request $request): JsonResponse
    {
        if ($request->get('check', false)) $product->update(['status' => Product::STATUS_ACTIVE]);
        else $product->update(['status' => Product::STATUS_DRAFT]);

        $product->moderation()->delete();

        return new JsonResponse(options: JSON_UNESCAPED_UNICODE);
    }
}
