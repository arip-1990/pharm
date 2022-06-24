<?php

namespace App\Http\Controllers\Api\V1\Product\Moderation;

use App\Models\Photo;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UpdateController
{
    public function handle(Product $product, Request $request): JsonResponse
    {
        if ($request->get('check', false)) {
            $product->update(['status' => Product::STATUS_ACTIVE]);
        }
        else {
            $photos = $product->photos()->where('status', Photo::STATUS_NOT_CHECKED)->get();
            foreach ($photos as $photo) {
                Storage::delete('images/original/' . $photo->file);
                $photo->delete();
            }

            $product->update(['status' => Product::STATUS_DRAFT]);
        }
        $product->moderation()->delete();

        return new JsonResponse();
    }
}
