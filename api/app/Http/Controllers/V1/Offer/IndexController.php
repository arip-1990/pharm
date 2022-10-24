<?php

namespace App\Http\Controllers\V1\Offer;

use App\Http\Resources\OfferResource;
use App\Models\City;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndexController
{
    public function handle(Product $product, Request $request): JsonResponse
    {
        return new JsonResponse(OfferResource::collection(
            $product->offers()->whereCity($request->cookie('city', City::query()->find(1)?->name))->get()
        ));
    }
}