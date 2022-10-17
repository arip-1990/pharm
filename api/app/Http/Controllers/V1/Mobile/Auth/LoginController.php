<?php

namespace App\Http\Controllers\V1\Mobile\Auth;

use App\Helper;
use App\Http\Requests\Mobile\Auth\LoginRequest;
use App\Models\User;
use App\UseCases\Auth\LoginService;
use App\UseCases\PosService;
use App\UseCases\User\PhoneVerifyService;
use Illuminate\Http\JsonResponse;

class LoginController
{
    public function __construct(private readonly LoginService $loginService, private readonly PhoneVerifyService $verifyService, private readonly PosService $posService) {}

    public function handle(LoginRequest $request): JsonResponse
    {
        try {
            $data = $this->posService->getBalance($request->get('userIdentifier'));
            if (!isset($data['contactID']))
                return new JsonResponse(['dataRequired' => ['fullName', 'birthday', 'password']]);

            $data = $this->loginService->phoneAuth($request->get('userIdentifier'), $request->get('password'));
        }
        catch (\DomainException $e) {
            if ($e->getCode() === 100033) {
                $user = User::where('phone', $request->get('userIdentifier'))->firstOrFail();
                $user->token = $this->verifyService->requestVerify($user->phone);
                $user->save();

                return new JsonResponse(['otp' => [
                    'attemptsLeft' => 1,
                    'message' => 'На номер ' . Helper::formatPhone($user->phone, true) . ' отправлено сообщение с кодом'
                ]]);
            }

            return new JsonResponse(['error' => ['message' => $e->getMessage()]], 500);
        }

        return new JsonResponse(['token' => $data['token']]);
    }
}
