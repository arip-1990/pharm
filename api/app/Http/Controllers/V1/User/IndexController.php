<?php

namespace App\Http\Controllers\V1\User;

use App\Http\Resources\UserResource;
use App\UseCases\PosService;
use App\UseCases\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function __construct(private readonly UserService $service, private readonly PosService $posService) {}

    public function handle(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (!$session = $request->session()->get('session'))
                return new JsonResponse('Не авторизован', 401);

            $data = $this->service->getInfo($user->id, $session);
            $balance = $this->posService->getBalance($user->phone);
            $data['cardNumber'] = $balance['cardNumber'];
            $data['cardChargedBonus'] = $balance['cardChargedBonus'];
            $data['cardWriteoffBonus'] = $balance['cardWriteoffBonus'];
            $data['childrenCount'] = $user->children_count;
        }
        catch (\DomainException $e) {
            return new JsonResponse([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }

        return new JsonResponse(new UserResource($data));
    }
}
