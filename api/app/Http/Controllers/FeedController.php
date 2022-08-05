<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedController
{
    public function handle(Request $request): JsonResponse
    {
        $data = [];
        Product::query()->whereIn('code', $request->get('codes', []))->each(function (Product $product) use (&$data) {
            $data[] = [
                'Картинки' => $product->checkedPhotos->map(fn($photo) => $photo->getUrl()),
                'Состав' => $product->getValue(30),
                'Фармакологическое действие' => $product->getValue(31),
                'Показания' => $product->getValue(32),
                'Применение при беременности и кормлении грудью' => $product->getValue(33),
                'Противопоказания' => $product->getValue(34),
                'Побочные действия' => $product->getValue(35),
                'Назначение' => $product->getValue(45),
                'Взаимодействие' => $product->getValue(47),
                'Передозировка' => $product->getValue(48)
            ];
        });

        return new JsonResponse($data);
    }
}
