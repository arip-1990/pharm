<?php

namespace App\Http\Controllers\V1\Auth;

use App\UseCases\Auth\LoginService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LogoutController extends Controller
{
    public function __construct(private readonly LoginService $service) {}

    public function handle(Request $request): JsonResponse
    {
        try {
            $this->service->logout($request->user(), $request->session()->get('session'));

            $request->session()->invalidate();
        }
        catch (\DomainException $e) {
            return new JsonResponse([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }

        return new JsonResponse();
    }
}
