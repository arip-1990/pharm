<?php

namespace App\Http\Controllers\Api\V1\Product\Moderation;

use App\Http\Resources\ProductResource;
use App\Models\ModerationProduct;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class IndexController
{
    public function handle(): JsonResponse
    {
        if (Auth::user()->moderationProducts()->count()) {
            $products = Auth::user()->moderationProducts->map(fn (ModerationProduct $item) => $item->product);
        }
        else {
            $products = Product::query()->where('status', Product::STATUS_MODERATION)
                ->doesntHave('moderation')->take(10)->get();
            foreach ($products as $product) {
                ModerationProduct::query()->create([
                    'type' => 'photo',
                    'user_id' => Auth::id(),
                    'product_id' => $product->id
                ]);
            }
        }

        return new JsonResponse(ProductResource::collection($products));
    }
}
