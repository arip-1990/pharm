<?php

namespace App\Http\Controllers\V1\Panel\Attribute;

use App\Http\Resources\AttributeResource;
use App\Product\Entity\Attribute;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(): JsonResponse
    {
        return new JsonResponse(AttributeResource::collection(Attribute::orderBy('type')->get()));
    }
}
