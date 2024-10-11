<?php

namespace App\Http\Controllers\V1\PhotoKids;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserPhotoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            return new JsonResponse($request->user()?->photo_kids ?? []);
        }
        catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()]);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->children_count = $request->get('userChildren', 0);
        $user->save();

        return new JsonResponse(status: 201);
    }
}
