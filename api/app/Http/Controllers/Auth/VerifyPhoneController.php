<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\VerifyPhoneRequest;
use App\Models\User;
use App\UseCases\Auth\RegisterService;
use App\UseCases\PosService;
use App\UseCases\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

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

            if ($token = $request->session()->get('token')) {
                $data = $this->registerService->verifySms($request, $token);
//                $token = $this->registerService->requestPhoneVerification($token);
//                $request->session()->put('token', $token);
            }
            else {
                $data = $this->posService->getBalance($user->phone, validationCode: $request->get('smsCode'));

                $id = $this->userService->updateInfo($user);
                $this->userService->setPassword($id, $user->password);
            }

            $user->id = $data['id'];
            $user->session = $data['sessionId'];
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

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}
