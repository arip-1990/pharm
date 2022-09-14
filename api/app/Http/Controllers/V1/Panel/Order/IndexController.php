<?php

namespace App\Http\Controllers\V1\Panel\Order;

use App\Models\Order;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(Request $request): ResourceCollection
    {
        $query = Order::query()->select('orders.*');
        if ($user = $request->get('userName')) {
            $query->where('user_id', $user);
        }

        if ($field = $request->get('orderField')) {
            switch ($field) {
                case 'userName':
                    $query->join('users', 'users.id', '=', 'orders.user_id')
                        ->orderBy('users.name', $request->get('orderDirection'));
                    break;
                case 'userPhone':
                    $query->join('users', 'users.id', '=', 'orders.user_id')
                        ->orderBy('users.phone', $request->get('orderDirection'));
                    break;
                case 'store':
                    $query->join('stores', 'stores.id', '=', 'orders.store_id')
                        ->orderBy('stores.name', $request->get('orderDirection'));
                    break;
                default:
                    $query->orderBy($field, $request->get('orderDirection'));
            }
        }
        else $query->orderByDesc('id');

        $orders = $query->paginate($request->get('pageSize', 10));

        return OrderResource::collection($orders);
    }
}
