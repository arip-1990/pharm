<?php

namespace App\Http\Controllers\V1\Order;

use App\Http\Resources\OrderResource;
use App\Order\Entity\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function __construct(private readonly OrderRepository $repository) {}

    public function index(Request $request): JsonResource
    {
        $orders = $this->repository->getByUser($request->user(), 15);
        return OrderResource::collection($orders);
    }
}
