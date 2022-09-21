<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefreshController
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
