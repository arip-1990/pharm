<?php

namespace App\Http\Controllers\V1\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class RefreshController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        try {
            $token = Auth::refresh();

            $request->session()->invalidate();
        }
        catch (\DomainException $e) {
            return new JsonResponse([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }

        return new JsonResponse([
            'accessToken' => $token,
            'expiresIn' => Auth::factory()->getTTL() * 60,
        ]);
    }
}
