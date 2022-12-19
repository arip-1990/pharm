<?php

namespace App\Http\Controllers\V1\Panel\Auth;

use App\Http\Requests\Panel\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function handle(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only(['email', 'password'])))
            return new JsonResponse('Учетные данные не совпадают', 401);

        $request->session()->regenerate();

        return new JsonResponse();
    }
}
