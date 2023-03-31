<?php

namespace App\Http\Controllers\V1\Panel\User;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ShowController extends Controller
{
    public function handle(User $user): JsonResponse
    {
        return new JsonResponse(new UserResource($user), options: JSON_UNESCAPED_UNICODE);
    }
}
