<?php

namespace App\Http\Controllers\V1\Auth\Verify;

use App\Http\Requests\Auth\VerifyPhoneRequest;
use App\Models\User;
use App\UseCases\Auth\LoginService;
use App\UseCases\Auth\PasswordService;
use App\UseCases\PosService;
use App\UseCases\User\PhoneVerifyService;
use App\UseCases\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;

class VerifyPhoneController extends Controller
{
    public function __construct(
        private readonly LoginService $loginService,
        private readonly UserService $userService,
        private readonly PhoneVerifyService $verifyService,
        private readonly PasswordService $passwordService,
        private readonly PosService $posService
    ) {}

    public function handle(VerifyPhoneRequest $request): JsonResponse
    {
        $data = $request->validated();
        try {
            if ($loginData = $request->session()->get('loginData')) {
                if (!$token = $request->session()->get('token'))
                    throw new \DomainException('Невалидный токен');

                $this->verifyService->verifyPhone($token, $data['smsCode']);
                $session = $this->loginService->login($loginData['login'], $loginData['password']);

                $request->session()->regenerate();
                $request->session()->put('session', $session);

                return new JsonResponse();
            }

            if ($phone = $request->session()->get('phone')) {
                if (!$token = $request->session()->get('token'))
                    throw new \DomainException('Невалидный токен');

                $this->verifyService->verifyPhone($token, $data['smsCode']);
                $request->session()->put('token', $this->passwordService->requestResetPassword($phone));

                return new JsonResponse();
            }

            if (!$request->session()->has('userId'))
                throw new \DomainException('Ошибка данных');

            $user = User::find($request->session()->get('userId'));
            $this->posService->getBalance($user->phone, validationCode: $data['smsCode']);

            $this->userService->updateInfo($user, [], $request->session()->get('session'));
            $this->userService->setPassword($user->id, $user->password);

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
