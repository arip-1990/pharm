<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\UseCases\Auth\LoginService;
use App\UseCases\User\PhoneVerifyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class LoginController extends Controller
{
    public function __construct(private readonly LoginService $loginService, private readonly PhoneVerifyService $verifyService) {}

    public function handle(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        try {
            $session = $this->loginService->login($data['login'], $data['password']);

            $request->session()->regenerate();
            $request->session()->put('session', $session);
        }
        catch (\DomainException $e) {
            $code = $e->getCode();
            $message = $e->getMessage();
            $request->session()->put('loginData', $data);

            if ($code === 0){
                try {
                    $token = $this->verifyService->requestVerify($data['login']);
                    $request->session()->put('token', $token);
                }
                catch (\DomainException $e) {
                    $code = $e->getCode();
                    $message = $e->getMessage();
                }
            }

            if ($code === 100033) {
                try {
                    $token = $this->verifyService->requestVerify($data['login']);
                    $request->session()->put('token', $token);
                }
                catch (\DomainException $e) {
                    $code = $e->getCode();
                    $message = $e->getMessage();
                }
            }

            return new JsonResponse([
                'code' => $code,
                'message' => $message
            ], 500);
        }

        return new JsonResponse();
    }
}
