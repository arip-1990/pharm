<?php

namespace App\Http\Controllers\Api\V2\Auth;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LoginController
{
    public function handle(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt(['phone' => $request->get('email'), 'password' => $request->get('password')], $request->filled('remember')) and
            !Auth::attempt(['email' => $request->get('email'), 'password' => $request->get('password')], $request->filled('remember')))
            return new JsonResponse(['message' => trans('auth.failed')], Response::HTTP_UNPROCESSABLE_ENTITY);

        $request->session()->regenerate();

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}
