<?php

namespace App\Http\Controllers\V1\PhotoKids;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserLikePhotoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $photo_ids = [];
        $photos = $request->user()?->likes_photo ?? [];
        foreach ($photos as $m){
            $photo_ids[] = ["id" => $m->id];
        }

        return new JsonResponse($photo_ids);
    }
}

