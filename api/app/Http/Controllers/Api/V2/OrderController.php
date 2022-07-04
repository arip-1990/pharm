<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class OrderController
{
    public function index(): JsonResource
    {
        return OrderResource::collection(Order::query()->where('user_id', Auth::id())->paginate(15));
    }
}
