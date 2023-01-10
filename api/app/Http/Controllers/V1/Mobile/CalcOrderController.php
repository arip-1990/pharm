<?php

namespace App\Http\Controllers\V1\Mobile;

use App\Helper;
use App\Http\Requests\Mobile\CalcOrderRequest;
use App\Http\Resources\Mobile\CalcOrderResource;
use App\Models\City;
use App\Models\Store;
use App\UseCases\Order\CalculateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class CalcOrderController extends Controller
{
    public function __construct(private readonly CalculateService $service) {}

    public function handle(CalcOrderRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            if (!$city = City::where('name', Helper::trimPrefixCity($validatedData['city'] ?? $validatedData['addressData']['settlement']))->first())
                throw new \DomainException('Город неизвестен');

            if (!count($validatedData['items']))
                throw new \DomainException('Нет товаров для расчета');

            $storeId = $validatedData['deliveryPickupId'] ?? $validatedData['preferredPickupId'] ?? null;
            $data = $this->service->handle($validatedData['items'], $city, Store::find($storeId), true);

            return new JsonResponse(new CalcOrderResource($data));
        }
        catch (\Exception $e) {
            return new JsonResponse([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
