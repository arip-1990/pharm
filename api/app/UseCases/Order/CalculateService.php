<?php

namespace App\UseCases\Order;

use App\Models\City;
use App\Models\Location;
use App\Models\Store;

class CalculateService
{
    public function handle(array $items, City $city, string $storeId = null): array
    {
        $data = [];
        $locationIds = Location::whereIn('city_id', $city->children()->pluck('id')->add($city->id))->pluck('id');
        $stores = Store::whereIn('id', config('data.mobileStores'))->whereIn('location_id', $locationIds)
            ->with(['offers' => function ($query) use ($items) {
                $query->where('quantity', '>', 0)
                    ->whereIn('product_id', array_column($items, 'privateId'));
            }])->get()->sortByDesc(fn(Store $store) => $storeId === $store->id ? 999 : $store->offers->count());

        $notItems = $items;
        foreach ($stores as $store) {
            if (!count($notItems)) break;

            $tmp = $this->calc($notItems, $store);
            if (count($tmp['items'])) {
                $data[$store->id]['items'] = $tmp['items'];
                $data[$store->id]['totalPrice'] = $tmp['totalPrice'];
            }

            $notItems = $tmp['notItems'];
        }

        return ['data' => $data, 'notItems' => $notItems];
    }

    private function calc(array $items, Store $store): array
    {
        $data = ['items' => [], 'notItems' => [], 'totalPrice' => 0];
        foreach ($items as $item) {
            $productId = $item['privateId'] ?? $item['id'];
            $quantity = $item['quantity'];

            if ($offer = $store->offers->firstWhere('product_id', $productId)) {
                if ($offer->quantity < $quantity) {
                    $data['notItems'][] = [
                        'id' => $productId,
                        'price' => $item['price'],
                        'quantity' => $quantity - $offer->quantity,
                        'discount' => 0,
                        'subtotal' => ($quantity - $offer->quantity) * $item['price'],
                        'error' => 'Товара нет в наличии'
                    ];
                    $quantity = $offer->quantity;
                }

                $subtotal = $offer->price * $quantity;
                $data['items'][] = [
                    'id' => $offer->product_id,
                    'price' => $offer->price,
                    'quantity' => $quantity,
                    'discount' => 0,
                    'subtotal' => $subtotal,
                    'deliveryGroup' => $store->id
                ];
                $data['totalPrice'] += $subtotal;
            }
            else {
                $data['notItems'][] = [
                    'id' => $productId,
                    'price' => $item['price'],
                    'quantity' => $quantity,
                    'discount' => 0,
                    'subtotal' => $item['subtotal'],
                    'error' => 'Товара нет в наличии'
                ];
            }
        }

        return $data;
    }
}
