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
        if (!Auth::attempt($request->only(['email', 'password']), $request['remember']))
            return response()->json('Учетные данные не совпадают', 401);

        Auth::user()->createToken($request->header('User-Agent'));

        return new JsonResponse();
    }
}
