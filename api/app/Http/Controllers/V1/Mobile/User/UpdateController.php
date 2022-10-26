<?php

namespace App\Http\Controllers\V1\Mobile\User;

use App\Http\Requests\Mobile\UpdateUserRequest;
use App\Models\User;
use App\UseCases\PosService;
use App\UseCases\User\UserService;
use Illuminate\Http\JsonResponse;

class UpdateController
{
    public function __construct(private readonly UserService $userService, private readonly PosService $posService) {}

    public function handle(UpdateUserRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            if (isset($data['name'])) {
                $fullName = explode(' ', $data['name']);
                $firstName = $fullName[1] ?? $fullName[0];
                $lastName = isset($fullName[1]) ? $fullName[0] : null;
                $middleName = $fullName[2] ?? null;
            }

            $user = User::find($data['userIdentifier']);
            $this->userService->updateInfo($user, [
                'firstName' => $firstName ?? null,
                'lastName' => $lastName ?? null,
                'middleName' => $middleName ?? null,
                'email' => $data['email'] ?? null,
                'birthDate' => $data['birthday'] ?? null
            ]);

            $data = $this->posService->getBalance($user->phone);
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
                'gender' => $user->getGenderLabel(),
                'age' => $user->birth_date?->age,
                'segments' => [],
                'cardNumber' => $data['cardNumber'],
                'bonuses' => $data['cardBalance'],
            ]
        ]);
    }
}