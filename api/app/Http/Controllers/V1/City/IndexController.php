<?php

namespace App\Http\Controllers\V1\City;

use App\Http\Resources\CityResource;
use App\Store\Entity\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(): JsonResponse
    {
        $cities = City::whereNull('parent_id')->get();
        return new JsonResponse(CityResource::collection($cities));
    }
}
