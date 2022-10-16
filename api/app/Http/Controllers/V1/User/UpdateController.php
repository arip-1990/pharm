<?php

namespace App\Http\Controllers\V1\User;

use App\Http\Requests\User\UpdateRequest;
use App\UseCases\User\UserService;
use Illuminate\Http\JsonResponse;

class UpdateController
{
    public function __construct(private readonly UserService $service) {}

    public function handle(UpdateRequest $request): JsonResponse
    {
        try {
            $this->service->updateInfo($request->user(), $request->validated());
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
