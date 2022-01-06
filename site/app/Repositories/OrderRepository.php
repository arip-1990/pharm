<?php

namespace App\Repositories;

use App\Entities\Order;
use App\Entities\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class OrderRepository
{
    public function getAll(Request $request): Collection
    {
        $current = (int)$request->get('page', 1);
        $pageSize = (int)$request->get('pageSize', 10);
        $query = Order::query()->select('orders.*');

        $field = $request->get('orderField');
        if ($field) {
            if ($field === 'user')
                $query->join('users', 'users.id', '=', 'orders.user_id')->orderBy('users.name', $request->get('orderDirection'));
            elseif ($field === 'store')
                $query->join('stores', 'stores.id', '=', 'orders.store_id')->orderBy('stores.name', $request->get('orderDirection'));
            else
                $query->orderBy($field, $request->get('orderDirection'));
        }
        
        $total = $query->count();
        $orders = $query->skip(($current - 1) * $pageSize)->take($pageSize)->get()->map(function (Order $order) {
            return [
                'id' => $order->id,
                'cost' => $order->cost,
                'paymentType' => $order->payment_type,
                'deliveryType' => $order->delivery_type,
                'status' => $order->status,
                'note' => $order->note,
                'cancel_reason' => $order->cancel_reason,
                'createdAt' => $order->created_at,
                'updatedAt' => $order->updated_at,
                'user' => [
                    'id' => $order->user->id,
                    'name' => $order->user->name
                ],
                'store' => [
                    'id' => $order->store->id,
                    'name' => $order->store->name
                ],
                'statuses' => $order->statuses->map(fn($status) => [
                    'value' => $status['value'],
                    'state' => $status['state'] ?? 2,
                    'createdAt' => $status['created_at']
                ]),
            ];
        });

        return new Collection([
            'current' => $current,
            'pageSize' => $pageSize,
            'total' => $total,
            'data' => $orders
        ]);
    }
}
