<?php

namespace App\Http\Controllers\V1\Mobile\User;

use App\Models\User;
use App\UseCases\PosService;
use App\UseCases\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndexController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly PosService $posService
    ) {}

    public function handle(Request $request): JsonResponse
    {
        try {
            $user = User::where('phone', $request->get('userIdentifier'))->firstOrFail();

            $data = $this->userService->getInfo($user->id);
            $balance = $this->posService->getBalance($user->phone);
        }
        catch (\Exception $e) {
            return new JsonResponse(['error' => ['message' => $e->getMessage()]], 500);
        }

        return new JsonResponse([
            'user' => [
                'id' => $user->id,
                'name' => $user->getFullName(),
                'phone' => $user->phone,
                'email' => $user->email,
                'emailConfirmed' => !!$user->email_verified_at,
                'gender' => $user->getGenderLabel(),
                'age' => $user->birth_date?->age,
                'segments' => [],
                'cardNumber' => $balance['cardNumber'],
                'bonuses' => $balance['cardNumber'],
                'units' => 'бонусов',
                'cardPercent' => 0,
                'pendingBonuses' => 0,
                'pendingBonusesTitle' => 'Будет накоплено',
                'expressBonuses' => 0,
                'exressBonusesTitle' => 'Экспресс-бонусы',
                'loyaltyProgram' => [],
            ]
        ]);
    }
}
