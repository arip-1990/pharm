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
            $fileName = explode('.', $fileName);

            $product->photos()->sync(Photo::create([
                'product_id' => $product->id,
                'title' => explode('.', $image->getClientOriginalName())[0],
                'name' => $fileName[0],
                'extension' => $fileName[1],
                'sort' => $sort ? $sort->sort + 1 : 0
            ]));
        }
        catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 500);
        }

        return new JsonResponse();
    }
}
