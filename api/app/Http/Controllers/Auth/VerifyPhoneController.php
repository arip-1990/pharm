<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\VerifyPhoneRequest;
use App\Models\User;
use App\UseCases\Auth\RegisterService;
use App\UseCases\PosService;
use App\UseCases\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class VerifyPhoneController
{
    public function __construct(
        private readonly RegisterService $registerService,
        private readonly UserService $userService,
        private readonly PosService $posService
    ) {}

    public function handle(VerifyPhoneRequest $request): JsonResponse
    {
        try {
            $user = new User($request->session()->get('userData'));

            if ($request->session()->has('token')) {
                $id = $this->registerService->verifySms($request);
            }
            else {
                $data = $this->posService->getBalance($user->phone, validationCode: $request->get('smsCode'));
                if ($data['ReturnCode'] !== 0)
                    throw new \DomainException('Проверочный код не корректный');

                $id = $this->userService->userUpdate($user);
                $this->userService->setPassword($id, $user->password);
            }

            $user->id = $id;
            $user->password = Hash::make($user->password);
            $user->save();

            $request->session()->flush();
        }
        catch (\DomainException $e) {
            return new JsonResponse($e->getMessage(), 500);
        }

        return new JsonResponse();
    }
}
