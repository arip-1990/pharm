<?php

namespace App\Http\Controllers\V1\Panel\Attribute;

use App\Http\Resources\AttributeResource;
use App\Models\Attribute;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(): JsonResponse
    {
        return new JsonResponse(AttributeResource::collection(Attribute::query()->orderBy('type')->get()), options: JSON_UNESCAPED_UNICODE);
    }
}
