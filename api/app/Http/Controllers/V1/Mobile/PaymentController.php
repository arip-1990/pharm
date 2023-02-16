<?php

namespace App\Http\Controllers\V1\Mobile;

use App\Http\Requests\Mobile\PaymentRequest;
use App\Http\Resources\Mobile\PaymentResource;
use App\Order\Entity\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class PaymentController extends Controller
{
    public function handle(PaymentRequest $request): JsonResponse
    {
        try {
            return new JsonResponse([
                'payments' => PaymentResource::collection(Payment::paginate($request->get('pageSize', 10)))
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
