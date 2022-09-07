<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\UseCases\Auth\LoginService;
use App\UseCases\PosService;
use App\UseCases\User\UserService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SetPasswordController
{
    public function __construct(
        private readonly UserService $service,
        private readonly LoginService $loginService,
        private readonly PosService $posService
    ) {}

    public function handle(Request $request): JsonResponse
    {
        try {
            $loginData = $request->session()->get('login');
            $data = $this->posService->getBalance($loginData['login']);
            if (!isset($data['ContactID']))
                throw new \DomainException('Нет пользователя');

            if (!$user = User::query()->find($data['ContactID'])) {
                $user = new User([
                    'id' => $data['ContactID'],
                    'first_name' => $data['FirstName'],
                    'birth_date' => Carbon::parse($data['BirthDate']),
                    'phone' => $data['Phone'],
                    'email' => $data['Email'],
                    'password' => $request->get('password')
                ]);
            }

            $this->service->setPassword($user->id, $user->password);
            $this->loginService->phoneAuth($user->phone, $user->password);

            $user->password = Hash::make($user->password);
            $user->save();
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
