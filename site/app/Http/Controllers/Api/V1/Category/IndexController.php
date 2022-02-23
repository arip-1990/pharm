<?php

namespace App\Http\Controllers\Api\V1\Category;

use App\Entities\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(): JsonResponse
    {
        try {
            $categories = Category::query()->get()->toTree();
        }
        catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 500);
        }

        return response()->json($categories);
    }
}
