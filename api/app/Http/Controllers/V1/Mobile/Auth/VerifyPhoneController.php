<?php

namespace App\Http\Controllers\V1\Mobile\Auth;

use App\Http\Requests\Mobile\Auth\VerifyPhoneRequest;
use App\Models\User;
use App\UseCases\PosService;
use App\UseCases\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class VerifyPhoneController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly PosService $posService
    ) {}

    public function handle(VerifyPhoneRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $user = User::where('phone', $data['userIdentifier'])->firstOrFail();

            $this->posService->getBalance($user->phone, validationCode: $data['otp']);

            $this->userService->updateInfo($user);
            $this->userService->setPassword($user->id, $user->password);

            $user->password = Hash::make($user->password);
            $user->save();
        }
        catch (\DomainException $e) {
            return new JsonResponse(['error' => ['message' => $e->getMessage()]], 500);
        }

        return new JsonResponse(['token' => Auth::login($user)]);
    }
}
