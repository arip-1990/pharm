<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Entities\Order;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(Request $request): ResourceCollection
    {
        $query = Order::query()->select('orders.*');
        $field = $request->get('orderField');
        if ($field) {
            if ($field === 'user')
                $query->join('users', 'users.id', '=', 'orders.user_id')
                    ->orderBy('users.name', $request->get('orderDirection'));
            elseif ($field === 'store')
                $query->join('stores', 'stores.id', '=', 'orders.store_id')
                    ->orderBy('stores.name', $request->get('orderDirection'));
            else
                $query->orderBy($field, $request->get('orderDirection'));
        }

        $orders = $query->paginate($request->get('pageSize', 10));

        return OrderResource::collection($orders);
    }
}
