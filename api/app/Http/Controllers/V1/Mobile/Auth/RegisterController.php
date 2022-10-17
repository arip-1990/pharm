<?php

namespace App\Http\Controllers\V1\Mobile\Auth;

use App\Helper;
use App\Http\Requests\Mobile\Auth\RegisterRequest;
use App\UseCases\Auth\LoginService;
use App\UseCases\Auth\RegisterService;
use Illuminate\Http\JsonResponse;

class RegisterController
{
    public function __construct(private readonly RegisterService $service, private readonly LoginService $loginService) {}

    public function handle(RegisterRequest $request): JsonResponse
    {
        try {
            $user = $this->service->requestRegister($request);
        }
        catch (\DomainException $e) {
            if ($e->getCode() === 111) {
                $data = $this->loginService->phoneAuth($request->get('userIdentifier'), $request->get('password'));
                return new JsonResponse(['token' => $data['token']]);
            }

            return new JsonResponse(['error' => ['message' => $e->getMessage()]], 500);
        }

        return new JsonResponse(['otp' => [
            'attemptsLeft' => 1,
            'message' => 'На номер ' . Helper::formatPhone($user->phone, true) . ' отправлено сообщение с кодом'
        ]]);
    }
}
