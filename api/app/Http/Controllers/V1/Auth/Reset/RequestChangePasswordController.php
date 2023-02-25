<?php

namespace App\Http\Controllers\V1\Auth\Reset;

use App\UseCases\Auth\PasswordService;
use App\UseCases\User\PhoneVerifyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class RequestChangePasswordController extends Controller
{
    public function __construct(private readonly PasswordService $passwordService, private readonly PhoneVerifyService $verifyService) {}

    public function handle(Request $request): JsonResponse
    {
        $phone = $request->get('phone');
        try {
            $request->session()->put('token', $this->passwordService->requestResetPassword($phone));
        }
        catch (\DomainException $e) {
            $code = $e->getCode();
            if ($code === 100033) {
                $request->session()->put('phone', $phone);
                $request->session()->put('token', $this->verifyService->requestVerify($phone));
            }

            return new JsonResponse([
                'code' => $code,
                'message' => $e->getMessage()
            ], 500);
        }

        return new JsonResponse();
    }
}
