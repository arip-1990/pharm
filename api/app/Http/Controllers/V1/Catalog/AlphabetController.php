<?php

namespace App\Http\Controllers\V1\Catalog;

use App\Store\Entity\City;
use App\Product\Entity\{Offer, Product};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AlphabetController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $productIds = Offer::select('product_id')->whereCity($request->cookie('city', City::find(1)?->name))
            ->groupBy('product_id')->pluck('product_id');

        $alphabet = Product::selectRaw('SUBSTRING(name, 1, 1) as abc')->distinct('abc')
            ->whereIn('id', $productIds)->pluck('abc');

        return new JsonResponse($alphabet);
    }
}
