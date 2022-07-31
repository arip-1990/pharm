<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\UseCases\Auth\LoginService;
use Illuminate\Http\JsonResponse;

class LoginController
{
    public function __construct(private readonly LoginService $service) {}

    public function handle(LoginRequest $request): JsonResponse
    {
        try {
            $this->service->phoneAuth($request);
        }
        catch (\DomainException $e) {
            return new JsonResponse(json_decode($e->getMessage(), true));
        }

        return new JsonResponse();
    }
}
