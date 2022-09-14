<?php

namespace App\Http\Controllers\V1\Panel\Order;

use App\Models\Order;
use App\Http\Resources\OrderResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ShowController extends Controller
{
    public function handle(Order $order): JsonResponse
    {
        return new JsonResponse(new OrderResource($order));
    }
}
