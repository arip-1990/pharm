<?php

namespace App\Http\Controllers\V1\Mobile;

use App\Http\Requests\Mobile\CheckoutRequest;
use App\Order\UseCase\CheckoutService;
use Illuminate\Http\JsonResponse;

class CheckoutController
{
    public function __construct(private readonly CheckoutService $service) {}

    public function handle(CheckoutRequest $request): JsonResponse
    {
        try {
            $orders = $this->service->checkoutMobile($request);

            return new JsonResponse([
                'message' => 'Ваш заказ зарегистрирован и отправлен в аптеку для подтверждения.',
                'orders' => $orders
            ]);
        }
        catch (\Exception $e) {
            return new JsonResponse([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
