<?php

namespace App\Http\Controllers\User;

use App\Http\Resources\UserResource;
use App\UseCases\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndexController
{
    public function __construct(private readonly UserService $service) {}

    public function handle(Request $request): JsonResponse
    {
        $data = $this->service->userInfo($request->user());
        return new JsonResponse(new UserResource($data));
    }
}
