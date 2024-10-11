<?php

namespace App\Http\Controllers\V1\PhotoKids;

use App\Http\Controllers\Controller;
use App\Models\AgeCategory;
use Illuminate\Http\JsonResponse;

class GetPhotoController extends Controller
{
    public function index(AgeCategory $age): JsonResponse
    {
        return new JsonResponse(
            $age->photos()
                ->where('published', true)
                ->with('age_category')
                ->withCount('user_likes')
                ->get()
        );

    }
}
