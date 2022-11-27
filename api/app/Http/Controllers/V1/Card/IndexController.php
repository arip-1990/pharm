<?php

namespace App\Http\Controllers\V1\Card;

use App\Http\Resources\CardResource;
use App\UseCases\CardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function __construct(private readonly CardService $service) {}

    public function handle(Request $request): JsonResponse
    {
        try {
            $data = $this->service->getAllByUser($request->user()->id, $request->session()->get('session'));
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
