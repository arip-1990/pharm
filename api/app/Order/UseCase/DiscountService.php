<?php

namespace App\Order\UseCase;

use App\Exceptions\OrderException;
use Illuminate\Http\JsonResponse;

class DiscountService{

    function calculateDiscount($carts): JsonResponse
    {
        if (!$carts->count()) {
            throw new OrderException('Нет товаров в корзине!');
        }

        $totalPrice = 0;
        $totalDiscountPrice = 0;

        $carts->transform(function ($cart, $key) use (&$totalPrice, &$totalDiscountPrice) {

            $quantity = $cart['quantity'];  # количестов товара в корзине
            $product = $cart['product'];
            $price = $cart['product']['minPrice'];
            $discountStep = $product['quantity']; # количество товаров на которую действует скидка механика


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

            $totalPrice += $price * $quantity;

            return $cart;
        });

        $totalPrice -= $totalDiscountPrice;

        return response()->json([
            'data' => $carts,
            'totalPrice' => round($totalPrice, 2),
            'totalDiscountPrice' => round($totalDiscountPrice, 2),
        ]);
    }

    function getDiscountStore($price, $product, $quantityProduct)
    {

        $discount = $product->discounts->first();

        if (!$discount) return $price;

        $sets = intdiv($quantityProduct, $discount->quantity); // сколько раз скидка применяется
        $totalDiscount = 0;

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

        // финальная цена за единицу товара
        $finalPricePerUnit = round(($price * $quantityProduct - $totalDiscount) / $quantityProduct);

        return $finalPricePerUnit;

    }

    function getDiscountStoreNew($stores)
    {


    }

}
