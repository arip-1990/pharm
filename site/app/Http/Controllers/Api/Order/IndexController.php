<?php

namespace App\Http\Controllers\Api\Order;

use App\Repositories\OrderRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function __construct(private OrderRepository $orderRepository) {}

    public function handle(Request $request): JsonResponse
    {
        try {
            $orders = $this->orderRepository->getAll($request);
        }
        catch (\Exception $exception) {
            return response()->json($exception->getMessage());
        }

        return response()->json($orders);
    }
}
