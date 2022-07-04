<?php

namespace App\Http\Controllers\V2;

use App\Http\Resources\CartResource;
use App\Models\CartItem;
use App\UseCases\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CartController
{
    public function __construct(private CartService $cartService) {}

    public function index(): JsonResponse
    {
        return new JsonResponse(CartResource::collection($this->cartService->getItems()));
    }

    public function store(string $id): JsonResponse
    {
        $this->cartService->add(new CartItem(['product_id' => $id, 'quantity' => 1]));
        return new JsonResponse(['total' => $this->cartService->getTotalQuantity()], Response::HTTP_ACCEPTED);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $this->cartService->set($id, $request->get('quantity', 1));
        return new JsonResponse(['total' => $this->cartService->getTotalQuantity()], Response::HTTP_ACCEPTED);
    }

    public function delete(string $id): JsonResponse
    {
        $this->cartService->remove($id);
        return new JsonResponse(['total' => $this->cartService->getTotalQuantity()], Response::HTTP_ACCEPTED);
    }

//    public function pharmacy(Request $request): View | RedirectResponse
//    {
//        $title = ' | Выбор аптеки';
//
//        if(!$total = $this->cartService->getItems()->count())
//            return redirect()->route('cart');
//
//        $productIds = $this->cartService->getItems()->pluck('product_id');
//
//        $stores = [];
//        /** @var Offer[] $offers */
//        $offers = Offer::query()->whereIn('product_id', $productIds)->whereCity($this->city)->get();
//        foreach ($offers as $offer) {
//            $cartQuantity = $this->cartService->getItem($offer->product_id)->quantity;
//            $stores[$offer->store_id]['store'] = $offer->store;
//            $stores[$offer->store_id]['products'][] = [
//                'price' => $offer->price,
//                'quantity' => $cartQuantity < $offer->quantity ? $cartQuantity : $offer->quantity,
//                'product' => $offer->product
//            ];
//        }
//        usort($stores, function ($a, $b) {
//            $res = count($b['products']) - count($a['products']);
//            if ($res) return $res;
//            else {
//                $price_a = 0;
//                $price_b = 0;
//                $quantity_a = 0;
//                $quantity_b = 0;
//                for ($i = 0; $i < count($a['products']); $i++) {
//                    $quantity_a = $a['products'][$i]['quantity'];
//                    $quantity_b = $b['products'][$i]['quantity'];
//                    $price_a += $quantity_a * $a['products'][$i]['price'];
//                    $price_b += $quantity_b * $b['products'][$i]['price'];
//                }
//                $res = $quantity_b - $quantity_a;
//                return $res ?: $price_a - $price_b;
//            }
//        });
//
//        $cartService = $this->cartService;
//
//        return view('catalog.pharmacy', compact('title', 'stores', 'total', 'cartService'));
//    }
}
