<?php

namespace App\Http\Controllers\V1\Panel\Order;

use App\Models\Order;
use App\UseCases\Order\GenerateDataService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class SendDataController
{
    public function handle(Order $order): JsonResponse
    {
        $service = new GenerateDataService($order);
        return new JsonResponse($service->generateSenData(Carbon::now()), options: JSON_UNESCAPED_UNICODE);
    }
}
