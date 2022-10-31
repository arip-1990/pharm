<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\UseCases\Auth\LoginService;
use App\UseCases\User\PhoneVerifyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LoginController
{
    public function __construct(private readonly LoginService $loginService, private readonly PhoneVerifyService $verifyService) {}

    public function handle(LoginRequest $request): JsonResponse
    {
        try {
            $data = $this->loginService->login($request->get('login'), $request->get('password'));

            $request->session()->regenerate();
            $request->session()->put('session', $data['session']);
        }
        catch (\DomainException $e) {
            $code = $e->getCode();
            $request->session()->put('loginData', $request->validated());
            if ($code === 100033) {
                $token = $this->verifyService->requestVerify($request->get('login'));
                $request->session()->put('token', $token);
            }

            return new JsonResponse([
                'code' => $code,
                'message' => $e->getMessage()
            ], 500);
        }

        return new JsonResponse([
            'accessToken' => $data['token'],
            'expiresIn' => Auth::factory()->getTTL() * 60,
        ]);
    }
}
