<?php

namespace App\Http\Controllers\V1\Panel\Order;

use App\Http\Resources\OrderItemResource;
use App\Order\Entity\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ShowItemsController extends Controller
{
    public function handle(Order $order): JsonResponse
    {
        try {
            $items = $order->user?->orders->map(fn(Order $order) => [...$order->items])->collapse() ?? [];
            return new JsonResponse(OrderItemResource::collection($items), options: JSON_UNESCAPED_UNICODE);
        }
        catch (\Exception $exception) {
            return new JsonResponse([
                'code' => $exception->getCode(),
                'message' => $exception->getMessage()
            ], 500, options: JSON_UNESCAPED_UNICODE);
        }
    }
}
