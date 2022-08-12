<?php

namespace App\Http\Controllers\Order\Checkout;

use App\UseCases\Order\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StoreController
{
    public function __construct(private readonly CheckoutService $service) {}

    public function handle(Request $request): JsonResponse
    {
        try {
            $stores = $this->service->getStores($request);

            return new JsonResponse($stores);
        }
        catch (\DomainException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
