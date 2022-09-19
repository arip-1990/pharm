<?php

namespace App\Http\Controllers\V1\Mobile;

use App\Http\Requests\Mobile\PaymentRequest;
use App\Http\Resources\Mobile\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class PaymentController extends Controller
{
    public function handle(PaymentRequest $request): JsonResponse
    {
        $payments = Payment::query()->paginate($request->get('pageSize', 10));
        return new JsonResponse([
            'payments' => PaymentResource::collection($payments)
        ]);
    }
}
