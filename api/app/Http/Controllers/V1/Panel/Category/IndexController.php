<?php

namespace App\Http\Controllers\V1\Panel\Category;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(): JsonResponse
    {
        return new JsonResponse(CategoryResource::collection(Category::all()->toTree()), options: JSON_UNESCAPED_UNICODE);
    }
}
