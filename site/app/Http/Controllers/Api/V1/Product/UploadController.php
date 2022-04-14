<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Models\Photo;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function handle(Product $product, Request $request): JsonResponse
    {
        if($request->hasFile('file') and $request->file('file')->isValid()) {
            try {
                $sort = $product->photos()->orderByDesc('sort')->first();
                $photo = new Photo(['product_id' => $product->id, 'sort' => $sort ? $sort->sort + 1 : 0]);
                $photo->save();

                $image = $request->file('file');
                if (!Storage::exists("images/original/{$product->id}"))
                    mkdir(Storage::path("images/original/{$product->id}"), recursive: true);

                $image->storeAs("images/original/{$product->id}", $photo->id . '.' . $image->getClientOriginalExtension());
            }
            catch (\Exception $e) {
                $photo->delete();
                return new JsonResponse($e->getMessage());
            }
        }

        return new JsonResponse();
    }
}
