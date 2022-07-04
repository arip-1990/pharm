<?php

namespace App\Http\Controllers\Api\V2;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CityController
{
    public function handle(Request $request): JsonResponse
    {
        return new JsonResponse(config('data.city'));
    }
}
