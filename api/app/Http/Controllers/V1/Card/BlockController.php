<?php

namespace App\Http\Controllers\V1\Card;

use App\UseCases\CardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BlockController extends Controller
{
    public function __construct(private readonly CardService $service) {}

    public function handle(Request $request, string $cardId): JsonResponse
    {
        try {
            $this->service->block($request->user()->id, $cardId, $request->session()->get('session'));
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
