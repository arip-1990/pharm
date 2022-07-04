<?php

namespace App\Http\Controllers\V1\Attribute;

use App\Http\Resources\AttributeResource;
use App\Models\Attribute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        return new JsonResponse(AttributeResource::collection(Attribute::query()->orderBy('type')->get()));
    }
}
