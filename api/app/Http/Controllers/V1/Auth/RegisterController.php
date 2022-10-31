<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\UseCases\Auth\RegisterService;
use Illuminate\Http\JsonResponse;

class RegisterController
{
    public function __construct(private readonly RegisterService $service) {}

    public function handle(RegisterRequest $request): JsonResponse
    {
        try {
            $user = $this->service->requestRegister($request);
            
            $request->session()->put('userId', $user->id);
        }
        catch (\DomainException $e) {
            return new JsonResponse([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }

        return new JsonResponse();
    }
}
