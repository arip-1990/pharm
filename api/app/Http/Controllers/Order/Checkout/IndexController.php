<?php

namespace App\Http\Controllers\Order\Checkout;

use App\Http\Requests\Order\CheckoutRequest;
use App\Models\Order;
use App\UseCases\Order\CheckoutService;
use Illuminate\Http\JsonResponse;

class IndexController
{
    public function __construct(private readonly CheckoutService $service) {}

    public function handle(CheckoutRequest $request): JsonResponse
    {
        $paymentUrl = null;
        try {
            $order = $this->service->checkoutWeb($request);
            if ($request->validated('payment') == Order::PAYMENT_TYPE_SBER)
                $paymentUrl = $this->service->paySber($order, 'https://120на80.рф/order/checkout/' . $order->id);
        }
        catch (\DomainException $e) {
            return new JsonResponse([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }

        return new JsonResponse([
            'id' => $order->id,
            'paymentUrl' => $paymentUrl
        ]);
    }
}
