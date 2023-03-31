<?php

namespace App\Http\Controllers\V1\Panel\User;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(): JsonResponse
    {
        return new JsonResponse(UserResource::collection(User::all()), options: JSON_UNESCAPED_UNICODE);
    }
}
