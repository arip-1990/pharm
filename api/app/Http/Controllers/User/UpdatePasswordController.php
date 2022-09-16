<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\UpdatePasswordRequest;
use App\UseCases\User\UserService;
use Illuminate\Http\JsonResponse;

class UpdatePasswordController
{
    public function __construct(private readonly UserService $service) {}

    public function handle(UpdatePasswordRequest $request): JsonResponse
    {
        try {
            $this->service->updatePassword($request->user(), $request);
        }
        catch (\DomainException $e) {
            return new JsonResponse([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }

        return new JsonResponse();
    }
}