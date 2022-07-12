<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogoutController
{
    public function handle(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}
