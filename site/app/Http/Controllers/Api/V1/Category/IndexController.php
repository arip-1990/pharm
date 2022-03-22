<?php

namespace App\Http\Controllers\Api\V1\Category;

use App\Models\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(): JsonResponse
    {
        return new JsonResponse(CategoryResource::collection(Category::all()->toTree()));
    }
}
