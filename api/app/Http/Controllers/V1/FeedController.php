<?php

namespace App\Http\Controllers\V1;

use App\Product\Entity\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FeedController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $data = [];
        Product::whereIn('code', $request->post('codes', []))->each(function (Product $product) use (&$data) {
            $data[] = [
                'Код' => $product->code,
                'Картинка' => $product->photos()->first()?->getUrl(),
                'Описание' => $product->description,
                'Показания' => $product->getValue(32),
                'Назначение' => $product->getValue(45),
                'Состав' => $product->getValue(30),
                'ФармакологическоеДействие' => $product->getValue(31),
                'Противопоказания' => $product->getValue(34),
                'ПобочныеДействия' => $product->getValue(35),
                'Взаимодействие' => $product->getValue(47),
                'Передозировка' => $product->getValue(48),
                'ПрименениеПриБеременности' => $product->getValue(33)
            ];
        });

        return new JsonResponse($data, options: JSON_UNESCAPED_UNICODE);
    }
}
