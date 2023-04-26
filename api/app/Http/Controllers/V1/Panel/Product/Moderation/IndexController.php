<?php

namespace App\Http\Controllers\V1\Panel\Product\Moderation;

use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use App\Product\Entity\{ModerationProduct, Product};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class IndexController
{
    public function handle(Request $request): JsonResponse
    {
        if ($request->user()->moderationProducts()->count()) {
            $products = $request->user()->moderationProducts->map(fn (ModerationProduct $item) => $item->product);
        }
        else {
            $products = Product::where('status', Product::STATUS_MODERATION)
                ->doesntHave('moderation')->take(100)->get();
            foreach ($products as $product) {
                ModerationProduct::create([
                    'type' => 'photo',
                    'user_id' => $request->user()->id,
                    'product_id' => $product->id
                ]);
            }
        }

        return new JsonResponse(ProductResource::collection($products));
    }
}
