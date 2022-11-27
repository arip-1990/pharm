<?php

namespace App\Http\Controllers\V1\Order;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function index(Request $request): JsonResource
    {
        return OrderResource::collection(Order::where('user_id', $request->user()->id)->paginate(15));
    }
}
