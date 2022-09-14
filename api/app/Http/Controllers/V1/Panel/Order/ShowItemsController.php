<?php

namespace App\Http\Controllers\V1\Panel\Order;

use App\Http\Resources\OrderItemResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ShowItemsController extends Controller
{
    public function handle(Order $order): JsonResponse
    {
        $items = $order->user->orders->map(fn(Order $order) => [...$order->items])->collapse();
        return new JsonResponse(OrderItemResource::collection($items));
    }
}
