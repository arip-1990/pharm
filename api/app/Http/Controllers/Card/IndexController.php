<?php

namespace App\Http\Controllers\Card;

use App\Http\Resources\CardResource;
use App\UseCases\CardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndexController
{
    public function __construct(private readonly CardService $service) {}

    public function handle(Request $request): JsonResponse
    {
        try {
            $data = $this->service->getAll($request->user());
        }
        catch (\DomainException $e) {
            return new JsonResponse([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }

        return new JsonResponse(CardResource::collection($data));
    }
}
