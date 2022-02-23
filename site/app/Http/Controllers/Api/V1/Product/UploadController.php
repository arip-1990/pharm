<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Entities\Photo;
use App\Entities\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Intervention\Image\Facades\Image;

class UploadController extends Controller
{
    public function handle(Product $product, Request $request): JsonResponse
    {
        if($request->hasFile('file') and $request->file('file')->isValid()) {
            $photo = new Photo(['product_id' => $product->id]);

            $image = $request->file('file');
            if (!file_exists(storage_path("app/public/images/original/{$product->id}")))
                mkdir(storage_path("app/public/images/original/{$product->id}"), recursive: true);

            $image->storeAs("images/original/{$product->id}", $photo->id . '.' . $image->getClientOriginalExtension());
//            Image::make($image)->save(storage_path("images/original/{$product->id}") . $photo->id . '.' . $image->getClientOriginalExtension());

            $photo->save();
        }

        return response()->json();
    }
}
