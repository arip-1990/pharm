<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Requests\Payment\PaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(PaymentRequest $request): JsonResponse
    {
        $query = Payment::query();
        $payments = $query->paginate($request->get('pageSize', 10));
        return new JsonResponse([
            'payments' => PaymentResource::collection($payments)
        ]);
    }
}
