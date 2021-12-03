<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Entities\CartItem;
use App\Entities\Offer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $title = $this->title . ' | Состав заказа';
        $city = $request->cookie('city', $this->defaultCity);
        $cartService = $this->cartService;

        return view('catalog.cart', compact('title', 'city', 'cartService'));
    }

    public function add(Request $request, string $id): JsonResponse
    {
        $this->cartService->add(CartItem::create($id, $request->post('total', 1)));
        return new JsonResponse(['total' => $this->cartService->getTotal()], Response::HTTP_ACCEPTED);
    }

    public function change(Request $request, string $id): JsonResponse
    {
        $this->cartService->set($id, $request['quantity']);
        return new JsonResponse(['total' => $this->cartService->getTotal()], Response::HTTP_ACCEPTED);
    }

    public function remove(string $id): JsonResponse
    {
        $this->cartService->remove($id);
        return new JsonResponse(['total' => $this->cartService->getTotal()], Response::HTTP_ACCEPTED);
    }

    public function pharmacy(Request $request): View | RedirectResponse
    {
        $title = $this->title . ' | Выбор аптеки';
        $city = $request->cookie('city', $this->defaultCity);

        if(!$total = $this->cartService->getItems()->count())
            return redirect()->route('cart');

        $productIds = $this->cartService->getItems()->pluck('product_id');

        $stores = [];
        /** @var Offer[] $offers */
        $offers = Offer::query()->whereIn('product_id', $productIds)->whereCity($city)->get();
        foreach ($offers as $offer) {
            $cartQuantity = $this->cartService->getItem($offer->product_id)->quantity;
            $stores[$offer->store_id]['store'] = $offer->store;
            $stores[$offer->store_id]['products'][] = [
                'price' => $offer->price,
                'quantity' => $cartQuantity < $offer->quantity ? $cartQuantity : $offer->quantity,
                'product' => $offer->product
            ];
        }
        usort($stores, function ($a, $b) {
            $res = count($b['products']) - count($a['products']);
            if ($res) return $res;
            else {
                $price_a = 0;
                $price_b = 0;
                $quantity_a = 0;
                $quantity_b = 0;
                for ($i = 0; $i < count($a['products']); $i++) {
                    $quantity_a = $a['products'][$i]['quantity'];
                    $quantity_b = $b['products'][$i]['quantity'];
                    $price_a += $quantity_a * $a['products'][$i]['price'];
                    $price_b += $quantity_b * $b['products'][$i]['price'];
                }
                $res = $quantity_b - $quantity_a;
                return $res ?: $price_a - $price_b;
            }
        });

        $cartService = $this->cartService;

        return view('catalog.pharmacy', compact('title', 'city', 'stores', 'total', 'cartService'));
    }
}
