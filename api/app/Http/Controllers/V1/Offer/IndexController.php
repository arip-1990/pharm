<?php

namespace App\Http\Controllers\V1\Offer;

use App\Http\Resources\OfferResource;
use App\Product\Entity\Product;
use App\Store\Entity\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(Product $product, Request $request): JsonResponse
    {
        return new JsonResponse(OfferResource::collection(
            $product->offers()->whereCity($request->cookie('city', City::find(1)?->name))->get()
        ));
    }
}
