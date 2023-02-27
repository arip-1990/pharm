<?php

namespace App\Http\Controllers\V1\Category;

use App\Http\Resources\CategoryResource;
use App\Product\Entity\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(): JsonResponse
    {
        return new JsonResponse(CategoryResource::collection(Category::whereNull('parent_id')->get()));
    }
}
