<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\UseCases\Auth\RegisterService;
use Illuminate\Http\JsonResponse;

class RegisterController
{
    public function __construct(private readonly RegisterService $service) {}

    public function handle(RegisterRequest $request): JsonResponse
    {
        try {
            $this->service->handle($request);
        }
        catch (\DomainException $e) {
            return new JsonResponse($e->getMessage());
        }

        return new JsonResponse();
    }
}
