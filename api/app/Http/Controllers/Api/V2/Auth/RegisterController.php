<?php

namespace App\Http\Controllers\Api\V2\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RegisterController
{
    public function handle(RegisterRequest $request): JsonResponse
    {
        Auth::login(User::register($request['name'], $request['email'], $request['phone'], $request['password']));
        $request->session()->regenerate();

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}
