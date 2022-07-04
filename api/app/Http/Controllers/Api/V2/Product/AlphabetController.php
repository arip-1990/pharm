<?php

namespace App\Http\Controllers\Api\V2\Product;

use App\Models\Offer;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlphabetController
{
    public function handle(Request $request): JsonResponse
    {
        $productIds = Offer::query()->select('product_id')
            ->whereCity($request->get('city', config('data.city')[0]))
            ->groupBy('product_id')->get()->pluck('product_id');

        $alphabet = Product::query()->selectRaw('SUBSTRING(name, 1, 1) as abc')->distinct('abc')
            ->whereIn('id', $productIds)->get()->pluck('abc');

        return new JsonResponse($alphabet);
    }
}
