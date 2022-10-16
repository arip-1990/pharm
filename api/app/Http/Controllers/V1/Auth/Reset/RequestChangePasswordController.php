<?php

namespace App\Http\Controllers\V1\Auth\Reset;

use App\UseCases\Auth\PasswordService;
use App\UseCases\User\PhoneVerifyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RequestChangePasswordController
{
    public function __construct(private readonly PasswordService $passwordService, private readonly PhoneVerifyService $verifyService) {}

    public function handle(Request $request): JsonResponse
    {
        try {
            $token = $this->passwordService->requestResetPassword($request->get('phone'));

            $request->session()->put('token', $token);
        }
        catch (\DomainException $e) {
            $code = $e->getCode();
            if ($code === 100033) {
                $token = $this->verifyService->requestVerify($request->get('phone'));
                $request->session()->put('token', $token);
            }

            return new JsonResponse([
                'code' => $code,
                'message' => $e->getMessage()
            ], 500);
        }

        return new JsonResponse();
    }
}
