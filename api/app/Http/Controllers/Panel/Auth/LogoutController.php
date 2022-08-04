<?php

namespace App\Http\Controllers\Panel\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();
        $request->session()->regenerate();

        return response()->json();
    }
}
