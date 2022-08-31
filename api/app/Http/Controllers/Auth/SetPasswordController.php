<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\UseCases\Auth\LoginService;
use App\UseCases\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SetPasswordController
{
    public function __construct(private readonly UserService $service, private readonly LoginService $loginService) {}

    public function handle(Request $request): JsonResponse
    {
        try {
            $loginData = $request->session()->get('login');
            $user = User::query()->firstWhere('phone', $loginData['login']);
            $this->service->setPassword($user->id, $request->get('password'));
            $this->loginService->phoneAuth($user->phone, $request->get('password'));
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
