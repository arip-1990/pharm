<?php

namespace App\Http\Controllers\V1\Mobile\Auth;

use App\Helper;
use App\Http\Requests\Mobile\Auth\AuthRequest;
use App\Models\User;
use App\UseCases\PosService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class AuthController
{
    public function __construct(private readonly PosService $posService) {}

    public function handle(AuthRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            if (isset($data['fullName']) and isset($data['birthday'])) {
                $fullName = explode(' ', $data['fullName']);
                $firstName = $fullName[1] ?? $fullName[0];
                $lastName = isset($fullName[1]) ? $fullName[0] : null;
                $middleName = $fullName[2] ?? null;

                $user = User::create([
                    'id' => Uuid::uuid4()->toString(),
                    'phone' => $data['userIdentifier'],
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'middle_name' => $middleName,
                    'password' => substr($data['userIdentifier'], -7),
                    'birth_date' => $data['birthday']
                ]);
            }
            elseif (!$user = User::where('phone', $request->validated('userIdentifier'))->first()) {
                return new JsonResponse(['dataRequired' => ['fullName', 'birthday']]);
            }

            $userId = $this->posService->getBalance($user->phone, true)['contactID'] ?? null;
            if (!$userId) {
                $userId = $this->posService->createCard($user)['contactID'];
                $this->posService->getBalance($user->phone, true);
            }

            $user->update(['id' => $userId]);
        }
        catch (\DomainException $e) {
            return new JsonResponse(['error' => ['message' => $e->getMessage()]], 500);
        }

        return new JsonResponse(['otp' => [
            'attemptsLeft' => 1,
            'message' => 'На номер ' . Helper::formatPhone($user->phone, true) . ' отправлено сообщение с кодом'
        ]]);
    }
}
