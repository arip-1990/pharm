<?php

namespace App\Http\Controllers\V1\Panel\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->regenerate();

        return new JsonResponse();
    }
}
