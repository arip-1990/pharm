<?php

namespace App\Http\Controllers\Order;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class IndexController
{
    public function index(): JsonResource
    {
        return OrderResource::collection(Order::query()->where('user_id', Auth::id())->paginate(15));
    }
}
