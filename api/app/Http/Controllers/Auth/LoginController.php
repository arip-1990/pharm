<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\UseCases\Auth\LoginService;
use App\UseCases\User\PhoneVerifyService;
use Illuminate\Http\JsonResponse;

class LoginController
{
    public function __construct(private readonly LoginService $loginService, private readonly PhoneVerifyService $verifyService) {}

    public function handle(LoginRequest $request): JsonResponse
    {
        try {
            $this->loginService->phoneAuth($request->get('login'), $request->get('password'));
            $request->session()->regenerate();
        }
        catch (\DomainException $e) {
            $code = $e->getCode();
            $request->session()->put('login', $request->validated());
            if ($code === 100033) {
                $token = $this->verifyService->requestVerify($request->get('login'));
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
