<?php

namespace App\Http\Controllers\Auth\Verify;

use App\UseCases\Auth\RegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RequestPhoneVerifyController
{
    public function __construct(private readonly RegisterService $registerService) {}

    public function handle(Request $request): JsonResponse
    {
        try {
            if (!$token = $request->session()->get('token'))
                throw new \DomainException('Ошибка');

            $this->registerService->requestPhoneVerification($token);
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
