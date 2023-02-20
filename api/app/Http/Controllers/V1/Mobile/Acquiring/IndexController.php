<?php

namespace App\Http\Controllers\V1\Mobile\Acquiring;

use App\Http\Requests\Mobile\Acquiring\IndexRequest;
use App\Order\UseCase\AcquiringService;
use Illuminate\Http\JsonResponse;

class IndexController
{
    public function __construct(private readonly AcquiringService $service) {}

    public function handle(IndexRequest $request): JsonResponse
    {
        $data = $request->validated();
        $successRegex = "{$data['orderId']}/success";
        $failureRegex = "{$data['orderId']}/failed";

        try {
            if ($data['command'] !== 'create' or $data['paymentMethodId'] != '1')
                throw new \DomainException('Ошибка данных');

            $data = $this->service->sberPay((int)$data['orderId']);
        }
        catch (\Exception $e) {
            return new JsonResponse([
                "error" => $e->getMessage(),
                "success" => false,
                "paymentId" => null,
                "paymentUrl" => null,
                "successRegex" => $successRegex,
                "failureRegex" => $failureRegex
            ]);
        }

        return new JsonResponse([
            "success" => true,
            "paymentId" => $data['paymentId'],
            "paymentUrl" => $data['paymentUrl'],
            "successRegex" => $successRegex,
            "failureRegex" => $failureRegex
        ]);
    }
}
