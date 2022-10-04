<?php

namespace App\Http\Controllers\V1\Mobile;

use App\Http\Requests\Mobile\CheckoutRequest;
use App\UseCases\Order\CheckoutService;
use Illuminate\Http\JsonResponse;

class CheckoutController
{
    public function __construct(private readonly CheckoutService $service) {}

    public function handle(CheckoutRequest $request): JsonResponse
    {
        try {
            $orders = $this->service->checkoutMobile($request);
        }
        catch (\DomainException $e) {
            return new JsonResponse([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }

        return new JsonResponse([
            'message' => 'Ваш заказ принят.',
            'orders' => $orders
        ]);
    }
}
