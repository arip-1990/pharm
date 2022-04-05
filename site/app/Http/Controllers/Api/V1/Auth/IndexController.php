<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function handle(): JsonResponse
    {
        return new JsonResponse(new UserResource(Auth::user()));
    }
}
