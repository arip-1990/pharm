<?php

namespace App\Http\Controllers\V2\User;

use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndexController
{
    public function handle(Request $request): JsonResponse
    {
        return new JsonResponse(new UserResource($request->user()));
    }
}
