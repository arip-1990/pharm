<?php

namespace App\Http\Controllers\V1\Order\Checkout;

use App\UseCases\Order\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreController
{
    public function __construct(private readonly CheckoutService $service) {}

    public function handle(Request $request): JsonResponse
    {
        try {
            $stores = $this->service->getStores($request);
        }
        catch (\DomainException $e) {
            return new JsonResponse([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }

        return new JsonResponse($stores);
    }
}
