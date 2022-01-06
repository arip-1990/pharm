<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\Api\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function handle(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only(['email', 'password']), $request['remember']))
            return response()->json('Учетные данные не совпадают', 401);

        Auth::user()->createToken($request->header('User-Agent'));

        return response()->json();
    }
}
