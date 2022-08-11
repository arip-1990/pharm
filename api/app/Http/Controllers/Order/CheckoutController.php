<?php

namespace App\Http\Controllers\Order;

use App\Http\Requests\Order\CheckoutRequest;
use Illuminate\Http\JsonResponse;

class CheckoutController
{
    public function handle(CheckoutRequest $request): JsonResponse
    {
        return new JsonResponse($request->validated());
    }
}
