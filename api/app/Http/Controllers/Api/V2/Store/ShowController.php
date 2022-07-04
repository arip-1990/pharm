<?php

namespace App\Http\Controllers\Api\V2\Store;

use App\Http\Resources\StoreResource;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ShowController extends Controller
{
    public function handle(Store $store): JsonResponse
    {
            return new JsonResponse(new StoreResource($store, true));
    }
}
