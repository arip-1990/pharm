<?php

namespace App\Http\Controllers\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\DeliveryRequest;
use App\Http\Resources\Mobile\DeliveryResource;
use App\Models\Delivery;
use Illuminate\Http\JsonResponse;

class DeliveryController extends Controller
{
    public function handle(DeliveryRequest $request): JsonResponse
    {
        return new JsonResponse([
            'deliveries' => DeliveryResource::customCollection(Delivery::all(), $request->validated())
        ]);
    }
}
