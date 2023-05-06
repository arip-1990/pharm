<?php

namespace App\Http\Controllers\V2\Setting\Banner;

use App\Http\Resources\Setting\BannerResource;
use App\Setting\Entity\Banner;
use Illuminate\Http\JsonResponse;

class IndexController
{
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(BannerResource::collection(Banner::orderBy('sort')->get()));
    }
}
