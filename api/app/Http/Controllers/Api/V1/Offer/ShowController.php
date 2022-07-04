<?php

namespace App\Http\Controllers\Api\V1\Offer;

use App\Http\Resources\OfferResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ShowController extends Controller
{
    public function handle(Product $product): JsonResponse
    {
        return new JsonResponse(new OfferResource($product));
    }
}
