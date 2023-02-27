<?php

namespace App\Http\Controllers\V1\Panel\Order;

use App\Http\Resources\OrderResource;
use App\Order\Entity\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ShowController extends Controller
{
    public function handle(Order $order): JsonResponse
    {
        try {
            return new JsonResponse(new OrderResource($order));
        }
        catch (\Exception $exception) {
            return new JsonResponse([
                'code' => $exception->getCode(),
                'message' => $exception->getMessage()
            ], 500);
        }
    }
}
