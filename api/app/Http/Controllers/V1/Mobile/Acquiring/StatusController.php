<?php

namespace App\Http\Controllers\V1\Mobile\Acquiring;

use App\Http\Requests\Mobile\Acquiring\StatusRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class StatusController
{
    public function handle(StatusRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (!Order::where('sber_id', $data['paymentId'])->first()) {
            return new JsonResponse([
                'success' => false,
                'paymentId' => $data['paymentId'],
                'paymentCaptured' => false
            ]);
        }

        return new JsonResponse([
            'success' => true,
            'paymentId' => $data['paymentId'],
            'paymentCaptured' => true
        ]);
    }
}
