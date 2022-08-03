<?php

namespace App\Http\Controllers;

use App\Http\Resources\CityResource;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CityController
{
    public function handle(Request $request): JsonResponse
    {
        $cities = City::query()->whereNull('parent_id')->get();
        return new JsonResponse(CityResource::collection($cities));
    }
}
