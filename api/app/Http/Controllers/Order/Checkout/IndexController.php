<?php

namespace App\Http\Controllers\Order\Checkout;

use App\Http\Requests\Order\CheckoutRequest;
use App\Models\Payment;
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
            if ($order->payment->equalType(Payment::TYPE_CARD))
                $paymentUrl = $this->service->paySber($order, 'https://xn--12080-6ve4g.xn--p1ai/order/checkout/' . $order->id);
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
