<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Http\Requests\Delivery\DeliveryRequest;
use App\Http\Resources\DeliveryResource;
use App\Models\DeliveryType;
use Illuminate\Http\JsonResponse;

class IndexController extends Controller
{
    public function handle(DeliveryRequest $request): JsonResponse
    {
        return new JsonResponse([
            'deliveries' => DeliveryResource::customCollection(DeliveryType::all(), $request->validated())
        ]);
    }
}
