<?php

namespace App\Http\Controllers\V1\Auth\Reset;

use App\UseCases\Auth\PasswordService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ValidateTempPasswordController extends Controller
{
    public function __construct(private readonly PasswordService $passwordService) {}

    public function handle(Request $request): JsonResponse
    {
        try {
            $token = $this->passwordService->validateTempPassword($request->session()->get('token'), $request->get('password'));
            $request->session()->put('token', $token);
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
