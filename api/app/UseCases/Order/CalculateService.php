<?php

namespace App\UseCases\Order;

use App\Models\City;
use App\Models\Delivery;
use App\Models\Location;
use App\Models\Store;

class CalculateService
{
    public function handle(array $items, City $city, string $storeId = null, int $deliveryId = null): array
    {
        $data = [];
        $locationIds = Location::whereCity($city)->pluck('id');
        $stores = Store::whereIn('id', config('data.mobileStores'))->whereIn('location_id', $locationIds)
            ->with(['offers' => fn ($query) => $query->whereIn('product_id', array_column($items, 'privateId'))])
            ->get()->sortByDesc(fn(Store $store) => $storeId === $store->id ? 999 : $store->offers->count());

        $notItems = $items;
        $switchDelivery = true;
        $delivery = Delivery::find($deliveryId ?? 2);
        foreach ($stores as $store) {
            if (!count($notItems)) break;

            $tmp = $this->calc($notItems, $store, $delivery);
            if (count($tmp['items'])) {
                $data[$store->id]['items'] = $tmp['items'];
                $data[$store->id]['delivery'] = $delivery->id;
                $data[$store->id]['totalPrice'] = $tmp['totalPrice'];
            }

            $notItems = $tmp['notItems'];
            if ($switchDelivery) {
                if ($delivery->isType(Delivery::TYPE_DELIVERY)) {
                    $delivery = Delivery::find(2);
                    $switchDelivery = false;
                }
                else $delivery = Delivery::find(1);
            }
        }

        return ['data' => $data, 'notItems' => $notItems];
    }

    private function calc(array $items, Store $store, Delivery $delivery): array
    {
        $data = ['items' => [], 'notItems' => [], 'totalPrice' => 0];
        foreach ($items as $item) {
            $productId = $item['privateId'] ?? $item['id'];
            $quantity = $item['quantity'];

            if (!$offer = $store->offers->firstWhere('product_id', $productId) or $offer->quantity < $quantity) {
                $data['notItems'][] = [
                    'id' => $productId,
                    'price' => $item['price'],
                    'quantity' => $quantity,
                    'discount' => 0,
                    'subtotal' => $item['subtotal'],
                    'error' => 'Товара нет в наличии'
                ];
            }
            else {
                $subtotal = $item['price'] * $quantity;
                $data['items'][] = [
                    'id' => $offer->product_id,
                    'price' => $item['price'],
                    'quantity' => $quantity,
                    'discount' => 0,
                    'subtotal' => $subtotal,
                    'deliveryGroup' => (string)$delivery->id
                ];
                $data['totalPrice'] += $subtotal;
            }
        }

        return $data;
    }
}
