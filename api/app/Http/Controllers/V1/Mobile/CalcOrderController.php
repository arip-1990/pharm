<?php

namespace App\Http\Controllers\V1\Mobile;

use App\Http\Requests\Mobile\CalcOrderRequest;
use App\Http\Resources\Mobile\CalcOrderResource;
use App\UseCases\Order\CalculateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class CalcOrderController extends Controller
{
    public function __construct(private readonly CalculateService $service) {}

    public function handle(CalcOrderRequest $request): JsonResponse
    {
        return new JsonResponse(
            new CalcOrderResource($this->service->handle($request->items, $request->deliveryPickupId))
        );
    }
}
