<?php

namespace App\Http\Controllers\V1\Setting\Banner;

use App\Http\Resources\Setting\BannerResource;
use App\Setting\Entity\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(BannerResource::collection(Banner::orderBy('sort')->get()), options: JSON_UNESCAPED_UNICODE);
    }
}
