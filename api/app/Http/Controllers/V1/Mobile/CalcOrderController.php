<?php

namespace App\Http\Controllers\V1\Mobile;

use App\Helper;
use App\Http\Requests\Mobile\CalcOrderRequest;
use App\Http\Resources\Mobile\CalcOrderResource;
use App\Models\City;
use App\UseCases\Order\CalculateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class CalcOrderController extends Controller
{
    public function __construct(private readonly CalculateService $service) {}

    public function handle(CalcOrderRequest $request): JsonResponse
    {
        $city = City::where('name', Helper::trimPrefixCity($request->city))->first();
        $data = ['items' => [], 'totalPrice' => 0];
        $tmp = $this->service->handle($request->items, $city, $request->deliveryPickupId);

        foreach ($tmp['data'] as $item) {
            array_push($data['items'], ...$item['items']);
            $data['totalPrice'] += $item['totalPrice'];
        }

        if (count($tmp['notItems'])) array_push($data['items'], ...$tmp['notItems']);

        return new JsonResponse(new CalcOrderResource($data));
    }
}
