<?php

namespace App\Http\Controllers\Order;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexController
{
    public function index(Request $request): JsonResource
    {
        return OrderResource::collection(Order::query()->where('user_id', $request->user()->id)->paginate(15));
    }
}
