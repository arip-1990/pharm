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
            if ($request->has('name')) {
                $fullName = $request->string('name')->explode(' ');
                if ($firstName = $fullName->get(1))
                    $lastName = $fullName->get(0);
                else
                    $firstName = $fullName->get(0);

                $middleName = $fullName->get(2);
            }

            if (!$user = User::where('id', $request->string('userIdentifier'))->orWhere('phone', $request->string('phone'))->first())
                throw new \DomainException('Пользователь не найден');

            $this->userService->updateInfo($user, [
                'firstName' => $firstName ?? null,
                'lastName' => $lastName ?? null,
                'middleName' => $middleName ?? null,
                'email' => $request->string('email'),
                'birthDate' => $request->date('birthday')
            ]);

            $data = $this->posService->getBalance($user->phone);

            return new JsonResponse([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->getFullName(),
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'gender' => $user->getGenderLabel(),
                    'age' => $user->birth_date?->age,
                    'cardNumber' => $data['cardNumber'],
                    'bonuses' => $data['cardBalance'],
                ]
            ]);
        }
        catch (\Exception $e) {
            return new JsonResponse([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
