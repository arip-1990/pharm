<?php

namespace App\Order\UseCase;

use App\Models\City;
use App\Models\Location;
use App\Models\Store;

class CalculateService
{
    public function handle(array $items, City $city, Store $store = null, bool $isMobile = false): array
    {
        if ($store) return $this->calc($items, $store);

        if ($isMobile) $storeIds = config('data.mobileStores')[$city->id];
        else $storeIds = Store::whereIn('location_id', Location::whereCity($city)->pluck('id'))->pluck('id');

        $store = Store::select('stores.*')->join('offers', 'stores.id', '=', 'offers.store_id')
            ->whereIn('stores.id', $storeIds)->whereIn('offers.product_id', array_column($items, 'privateId'))
            ->groupBy('stores.id')->orderByRaw('count(stores.id) desc')->first();

        return $this->calc($items, $store);
    }

    private function calc(array $items, Store $store): array
    {
        $data = ['items' => [], 'totalPrice' => 0];
        $offers = $store->offers()->where('quantity', '>', 0)
            ->whereIn('product_id', array_column($items, 'privateId') ?: array_column($items, 'id'))->get();
        foreach ($items as $item) {
            $productId = $item['privateId'] ?? $item['id'];

            if ((isset($item['deliveryGroup']) and $item['deliveryGroup'] == '3') or !$offer = $offers->firstWhere('product_id', $productId)) {
                $data['items'][] = $this->generateItem($productId, $item['name'], $item['price'], $item['quantity'], ['3']);
            }
            else {
                $error = null;
                if ($item['quantity'] > $offer->quantity) $error = "Доступно всего {$offer->quantity} количество";

                $data['items'][] = $this->generateItem($productId, $item['name'], $item['price'], $item['quantity'], ['2', '3'], $error);
            }

            $data['totalPrice'] += $item['price'] * $item['quantity'];
        }

        return $data;
    }

    private function generateItem(string $id, string $name, float $price, int $quantity, array $deliveries = null, string $error = null): array
    {
        $data = [
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity,
            'discount' => 0,
            'subtotal' => $price * $quantity
        ];

        if ($deliveries) $data['deliveryGroups'] = $deliveries;
        if ($error) $data['error'] = $error;

        return $data;
    }
}
