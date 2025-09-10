<?php

namespace App\Http\Controllers\V2\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Product\Entity\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpecialOfferController extends Controller
{
    public function __invoke(Request $request)
    {
        // Получаем id из запроса
        $ids = $request->input('id');

        // Если строка "12,44" → превращаем в массив
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        // Если ничего не передано → пустой массив
        $ids = (array) $ids;

        // Получаем товары
        $products = Product::query()
            ->whereIn('code', $ids)
            ->get();

        return ProductResource::collection($products);
    }
}
