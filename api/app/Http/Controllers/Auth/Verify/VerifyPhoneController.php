<?php

namespace App\Http\Controllers\Auth\Verify;

use App\Http\Requests\Auth\VerifyPhoneRequest;
use App\Models\User;
use App\UseCases\Auth\LoginService;
use App\UseCases\Auth\RegisterService;
use App\UseCases\PosService;
use App\UseCases\User\PhoneVerifyService;
use App\UseCases\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class VerifyPhoneController
{
    public function __construct(
        private readonly RegisterService $registerService,
        private readonly LoginService $loginService,
        private readonly UserService $userService,
        private readonly PhoneVerifyService $verifyService,
        private readonly PosService $posService
    ) {}

    public function handle(VerifyPhoneRequest $request): JsonResponse
    {
        try {
            if ($loginData = $request->session()->get('loginData')) {
                if (!$token = $request->session()->get('token'))
                    throw new \DomainException('Ошибка');

                $this->verifyService->verifyPhone($token, $request->get('smsCode'));
                $data = $this->loginService->phoneAuth($loginData['login'], $loginData['password']);

                $request->session()->regenerate();
                $request->session()->put('session', $data['session']);

                return new JsonResponse([
                    'accessToken' => $data['token'],
                    'expiresIn' => Auth::factory()->getTTL() * 60,
                ]);
            }

            if (!$request->session()->has('token') and !$request->session()->has('userId'))
                throw new \DomainException('Ошибка');

            if ($token = $request->session()->get('token')) {
                $user = User::query()->firstWhere('token', $token);
                $data = $this->registerService->verifySms($request, $token);
                $user->id = $data['id'];
            }
            else {
                $user = User::query()->find($request->session()->get('userId'));
                $this->posService->getBalance($user->phone, validationCode: $request->get('smsCode'));

                $this->userService->updateInfo($user, [], $request->session()->get('session'));
                $this->userService->setPassword($user->id, $user->password);
            }

            $user->password = Hash::make($user->password);
            $user->save();

            $request->session()->flush();
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
