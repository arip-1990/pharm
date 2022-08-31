<?php

namespace App\Http\Controllers\User;

use App\Http\Resources\UserResource;
use App\UseCases\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndexController
{
    public function __construct(private readonly UserService $service) {}

    public function handle(Request $request): JsonResponse
    {
        try {
            $data = $this->service->getInfo($request->user());
        }
        catch (\DomainException $e) {
            return new JsonResponse([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }

        return new JsonResponse(new UserResource($data));
    }
}
