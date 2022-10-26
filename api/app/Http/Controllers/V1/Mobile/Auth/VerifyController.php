<?php

namespace App\Http\Controllers\V1\Mobile\Auth;

use App\Http\Requests\Mobile\Auth\VerifyRequest;
use App\Models\User;
use App\UseCases\PosService;
use App\UseCases\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class VerifyController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly PosService $posService
    ) {}

    public function handle(VerifyRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $user = User::where('phone', $data['userIdentifier'])->firstOrFail();

            $data = $this->posService->getBalance($user->phone, validationCode: $data['otp']);
            if (!$user->phone_verified_at) {
                $this->userService->updateInfo($user);

                if (strlen($user->password) < 50) {
                    $this->userService->setPassword($user->id, $user->password);
                    $user->update(['password' => Hash::make($user->password)]);
                }
            }
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
