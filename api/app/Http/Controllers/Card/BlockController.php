<?php

namespace App\Http\Controllers\Card;

use App\UseCases\CardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlockController
{
    public function __construct(private readonly CardService $service) {}

    public function handle(Request $request, string $cardId): JsonResponse
    {
        try {
            $this->service->block($request->user(), $cardId);
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