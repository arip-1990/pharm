<?php

namespace App\Http\Controllers\V1\Cart;
use App\Exceptions\OrderException;
use App\Http\Controllers\Controller;
use App\Order\UseCase\DiscountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalculateDiscount extends Controller
{
    protected DiscountService $service;

    public function __construct(DiscountService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $carts = $request->collect();
        return $this->service->calculateDiscount($carts);
    }

}
