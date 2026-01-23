<?php

namespace App\Order\UseCase;

use App\Exceptions\OrderException;
use App\Product\Entity\Offer;
use App\Store\Entity\Store;
use Illuminate\Http\JsonResponse;

class DiscountService{

    public array $combo = [];
    public array $disableCombo = [];

    function calculateDiscount($carts): JsonResponse
    {
        if (!$carts->count()) {
            throw new OrderException('Нет товаров в корзине!');
        }

        $totalPrice = 0;
        $totalDiscountPrice = 0;

        $carts->transform(function ($cart, $key) use (&$totalPrice, &$totalDiscountPrice) {

            $quantity = $cart['quantity'];  # количестов товара в корзине
            $product = &$cart['product'];
            $price = $cart['product']['minPrice'];
            $discountStep = $product['quantity']; # количество товаров на которую действует скидка механика


            if (isset($product['discountObject']['combo'])) {
                if (!isset($this->combo[$product['discountObject']['id']])) {
                    $this->combo[$product['discountObject']['id']] = [];
                }
                if (count($this->combo[$product['discountObject']['id']]) < 2) {
                    $this->combo[$product['discountObject']['id']][] = [
                        "productId" => $product,
                        "price" => $price,
                        "quantity" => $quantity,
                        "percent" => $product['discount']
                    ];
                }else{
                    $this->disableCombo[] = &$product;
                }
            }

            if ($product['combo'] == null) {

                if ($discountStep > 1 && $product['rubles'] > 0 && intdiv($quantity, $discountStep) > 0) {
                    $cart['product']['discountPrice'] = round($price - intdiv($quantity, $discountStep) * $product['rubles'], 2);
                    $totalDiscountPrice += intdiv($quantity, $discountStep) * $product['rubles'];
                }

                if ($discountStep > 1 && $product['discount'] > 0 && $product['discount'] && intdiv($quantity, $discountStep) > 0){
                    $cart['product']['discountPrice'] = round($price - intdiv($quantity, $discountStep) * ($price * ($product['discount'] / 100)), 2);
                    $totalDiscountPrice += intdiv($quantity, $discountStep) * ($price * ($product['discount'] / 100));
                }

                if ($product['rubles'] && $product['rubles'] > 0 && $discountStep == 1) {
                    $cart['product']['discountPrice'] = round($price - $product['rubles'], 2);
                    $totalDiscountPrice += $product['rubles'] * $quantity;
                }

                if ($product['discount'] && $product['discount'] > 0 && $discountStep == 1) {
                    $cart['product']['discountPrice'] = round($price - ($price * ($product['discount'] / 100)), 2);
                    $totalDiscountPrice += ($price * ($product['discount'] / 100)) * $quantity;
                }
            }
            $totalPrice += $price * $quantity;
            return $cart;
        });

        if (count($this->disableCombo) != 0) {
            foreach ($this->disableCombo as &$product) {
                $product['combo'] = null;
                $product['discount'] = 0;
                $product['rubles'] = 0;
                $product['discountPrice'] = 0;
            }
        }




        $totalDiscountPrice += $this->calcCombo();
        $totalPrice -= $totalDiscountPrice;

        return response()->json([
            'data' => $carts,
            'totalPrice' => round($totalPrice, 2),
            'totalDiscountPrice' => round($totalDiscountPrice, 2),
        ]);
    }

    function calcCombo(): int|float
    {
        $minQuantity = 10000000000;
        $percent = 0;
        foreach ($this->combo as $comboList) {
            foreach ($comboList as $products) {
                if ($minQuantity > $products['quantity']) {
                    $minQuantity = $products['quantity'];
                }
                $percent = $products['percent'];
            }
        }
        $discountPrice = 0;
        $oneProductPercent = $percent / 2; // скидка в процентах для одного товара 25/2=12.5 пример

        foreach ($this->combo as &$comboList) {
            foreach ($comboList as &$products){
                $discountPrice += ($products['price'] * $minQuantity) * $percent / 100;
                $products['productId']['discountPrice'] = $products['price'] - ($products['price'] * $oneProductPercent / 100);
            }
        }
        return $discountPrice;
    }



    public array $storeCombo = []; // [store_id => [[products1, quantity, discount], products2 ...]]

    function getDiscountStore($validComboProducts, $store_id, $price, $product, $quantityProduct)
    {

        $discount = $product->discounts->first();

        if (!$discount) return $price;

        $comboKey = $discount['combo'];
        if ($comboKey != null && isset($validComboProducts[$comboKey]) && in_array($product['id'], $validComboProducts[$comboKey])) {

            // Если аптека ещё не добавлена, инициализируем
            if (!isset($this->storeCombo[$store_id])) {
                $this->storeCombo[$store_id] = [
                    "store_id" => $store_id,
                    "percent" => $discount['percent'] / 2,
                    "products" => []
                ];
            }

            // Добавляем продукт с quantity
            $this->storeCombo[$store_id]['products'][] = [
                "id" => $product['id'],
                "quantity" => $quantityProduct
            ];
        }


        if ($discount->quantity == 0) return $price;
        $totalDiscount = 0;

        if ($comboKey == null) {
            $sets = intdiv($quantityProduct, $discount->quantity); // сколько раз скидка применяется

            if ($discount->rubles > 0 && $discount->quantity > 1) {
                // Скидка в рублях на набор
                $totalDiscount = $sets * $discount->rubles;
            } elseif ($discount->rubles > 0 && $discount->quantity == 1) {
                // Скидка в рублях на каждую штуку
                $totalDiscount = $quantityProduct * $discount->rubles;
            } elseif ($discount->percent > 0 && $discount->quantity == 1) {
                // Скидка в процентах на каждую штуку
                $totalDiscount = $quantityProduct * ($price * ($discount->percent / 100));
            } elseif ($discount->percent > 0 && $discount->quantity > 1) {
                // Скидка в процентах на каждые quantity штук
                $totalDiscount = $sets * ($discount->quantity * $price * ($discount->percent / 100));
            }

        }

        $finalPricePerUnit = round(($price * $quantityProduct - $totalDiscount));
        return $finalPricePerUnit;

    }

    public function uniqueValidCombo(): array
    {
        return array_filter($this->storeCombo, function($store) {
            return isset($store['products']) && count($store['products']) === 2;
        });
    }


}
