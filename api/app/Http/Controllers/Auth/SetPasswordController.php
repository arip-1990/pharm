<?php

namespace App\Http\Controllers\Auth;

use App\UseCases\Auth\LoginService;
use App\UseCases\PosService;
use App\UseCases\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SetPasswordController
{
    public function __construct(
        private readonly UserService $service,
        private readonly LoginService $loginService,
        private readonly PosService $posService
    ) {}

    public function handle(Request $request): JsonResponse
    {
        try {
            $loginData = $request->session()->get('login');
            $data = $this->posService->getBalance($loginData['login']);
            if (!isset($data['contactID']))
                throw new \DomainException('Нет пользователя');

            $this->service->setPassword($data['contactID'], $request->get('password'));
            $this->loginService->phoneAuth($loginData['login'], $request->get('password'));
            $request->session()->regenerate();
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
