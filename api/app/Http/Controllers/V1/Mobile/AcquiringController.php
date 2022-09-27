<?php

namespace App\Http\Controllers\V1\Mobile;

use App\Http\Requests\Mobile\AcquiringRequest;
use App\UseCases\AcquiringService;
use Illuminate\Http\JsonResponse;

class AcquiringController
{
    public function __construct(private readonly AcquiringService $service) {}

    public function handle(AcquiringRequest $request): JsonResponse
    {
        $data = $request->validated();
        $tmp = [
            "success" => false,
            "paymentId" => null,
            "paymentUrl" => null,
            "successRegex" => "payment/(.+)/success",
            "failureRegex" => "payment/(.+)/failed"
        ];

        if ($data['command'] !== 'create' or explode('/', $data['paymentMethodId'])[1] !== 'card')
            return new JsonResponse($tmp);

        $this->service->sber((int)$data['orderId'], $data['returnUrl']);

        return new JsonResponse($data);
    }
}
