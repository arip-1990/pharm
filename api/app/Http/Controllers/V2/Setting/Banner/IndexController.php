<?php

namespace App\Http\Controllers\V2\Setting\Banner;

use App\Http\Resources\Setting\BannerResource;
use App\Setting\Entity\Banner;
use App\Setting\Entity\BannerType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndexController
{
    public function __invoke(Request $request): JsonResponse
    {
        $query = Banner::orderBy('sort');
        if ($request->get('type') != 'all') $query->whereNot('type', BannerType::MOBILE);

        return new JsonResponse(BannerResource::collection($query->get()));
    }
}
