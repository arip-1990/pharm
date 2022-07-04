<?php

namespace App\Http\Controllers\V2\Category;

use App\Models\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(): JsonResponse
    {
        return new JsonResponse(CategoryResource::collection(Category::query()->whereNull('parent_id')->get()));
    }
}
