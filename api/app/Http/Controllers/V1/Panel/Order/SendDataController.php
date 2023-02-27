<?php

namespace App\Http\Controllers\V1\Panel\Order;

use App\Order\Entity\Order;
use App\Order\UseCase\GenerateDataService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class SendDataController
{
    public function handle(Order $order): JsonResponse
    {
        try {
            $service = new GenerateDataService($order);
            return new JsonResponse($service->generateSenData(Carbon::now()), options: JSON_UNESCAPED_UNICODE);
        }
        catch (\Exception $exception) {
            return new JsonResponse([
                'code' => $exception->getCode(),
                'message' => $exception->getMessage()
            ], 500);
        }
    }
}
