<?php

namespace App\Http\Controllers\V1\Panel\Product;

use App\Models\Photo;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function handle(Product $product, Request $request): JsonResponse
    {
        if($request->hasFile('file') and $request->file('file')->isValid()) {
            $image = $request->file('file');
            do {
                $fileName = Str::random() . '.' . $image->getClientOriginalExtension();
            }
            while (Storage::exists('images/original/' . $fileName));

            $image->storeAs('images/original', $fileName);

            $sort = $product->photos()->orderByDesc('sort')->first();
            Photo::query()->create(['product_id' => $product->id, 'file' => $fileName, 'sort' => $sort ? $sort->sort + 1 : 0]);
        }

        return new JsonResponse();
    }
}
