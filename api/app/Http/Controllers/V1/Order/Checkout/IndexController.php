<?php

namespace App\Http\Controllers\V1\Order\Checkout;

use App\Http\Requests\Order\CheckoutRequest;
use App\Models\Payment;
use App\UseCases\AcquiringService;
use App\UseCases\Order\CheckoutService;
use Illuminate\Http\JsonResponse;

class IndexController
{
    public function __construct(
        private readonly CheckoutService $checkoutService,
        private readonly AcquiringService $acquiringService
    ) {}

    public function handle(CheckoutRequest $request): JsonResponse
    {
        $paymentUrl = null;
        try {
            $order = $this->checkoutService->checkoutWeb($request);
            if ($order->payment->equalType(Payment::TYPE_CARD))
                $paymentUrl = $this->acquiringService->sberPay($order->id)['paymentUrl'];
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
