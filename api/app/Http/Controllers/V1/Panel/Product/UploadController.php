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
        try {
            if (!$request->hasFile('file') or !$request->file('file')->isValid())
                throw new \DomainException('Файл не определен!');

            $image = $request->file('file');
            do {
                $fileName = Str::random() . '.' . strtolower($image->getClientOriginalExtension());
            }
            while (Storage::exists('images/original/' . $fileName));

            if (!$image->storeAs('images/original/', $fileName))
                throw new \DomainException('Не удалось сохранить фото');

            $sort = $product->photos()->orderByDesc('sort')->first();

            $photo = new Photo([
                'product_id' => $product->id,
                'title' => explode('.', $image->getClientOriginalName())[0],
                'file' => $fileName,
                'sort' => $sort ? $sort->sort + 1 : 0
            ]);
            $photo->creator()->associate($request->user());
            $photo->save();

            $product->photos()->syncWithoutDetaching($photo);
        }
        catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 500, options: JSON_UNESCAPED_UNICODE);
        }

        return new JsonResponse(options: JSON_UNESCAPED_UNICODE);
    }
}
