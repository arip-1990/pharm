<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\VerifyPhoneRequest;
use App\Models\User;
use App\UseCases\Auth\VerifyService;
use Illuminate\Http\JsonResponse;

class VerifyPhoneController
{
    public function __construct(private readonly VerifyService $service) {}

    public function handle(VerifyPhoneRequest $request): JsonResponse
    {
        try {
            $token = $request->session()->get('token');
            if (!$user = User::query()->where('token', $token)->first())
                throw new \DomainException('Невалидный токен');

            $token = $this->service->handle($request);
        }
        catch (\DomainException $e) {
            return new JsonResponse($e->getMessage());
        }

        return new JsonResponse();
    }
}
