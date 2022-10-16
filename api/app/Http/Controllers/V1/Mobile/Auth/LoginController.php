<?php

namespace App\Http\Controllers\V1\Mobile\Auth;

use App\Http\Requests\Mobile\Auth\LoginRequest;
use App\UseCases\Auth\LoginService;
use App\UseCases\User\PhoneVerifyService;
use Illuminate\Http\JsonResponse;

class LoginController
{
    public function __construct(private readonly LoginService $loginService, private readonly PhoneVerifyService $verifyService) {}

    public function handle(LoginRequest $request): JsonResponse
    {
        try {
            $data = $this->loginService->phoneAuth($request->get('userIdentifier'), $request->get('password'));
        }
        catch (\DomainException $e) {
            $code = $e->getCode();
            if ($code === 100033) {
                $token = $this->verifyService->requestVerify($request->get('login'));
                $request->session()->put('token', $token);
            }

            return new JsonResponse([
                'code' => $code,
                'message' => $e->getMessage()
            ], 500);
        }

        return new JsonResponse(['token' => $data['token']]);
    }
}
