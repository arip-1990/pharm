<?php

namespace App\Http\Controllers\V1\Order\Checkout;

use App\Order\UseCase\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class StoreController extends Controller
{
    public function __construct(private readonly CheckoutService $service) {}

    public function handle(Request $request): JsonResponse
    {
        try {
            return new JsonResponse($this->service->getStores($request));
        }
        catch (\Exception $e) {
            return new JsonResponse([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
