<?php

namespace App\Http\Controllers\V1\Panel\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        return new JsonResponse($request->user());
    }
}
