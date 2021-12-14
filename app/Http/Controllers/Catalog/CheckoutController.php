<?php

namespace App\Http\Controllers\Catalog;

use App\Entities\Offer;
use App\Entities\Order;
use App\Entities\Store;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\CheckoutRequest;
use App\UseCases\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CheckoutController extends Controller
{
    public function __construct(private OrderService $orderService)
    {
        parent::__construct();
    }

    public function index(Request $request, Store $store): View | RedirectResponse
    {
        $title = $this->title . ' | Состав заказа';
        $city = $request->cookie('city', $this->defaultCity);
        if (!$this->cartService->getItems()->count()) return redirect()->route('cart');

        $this->cartService->setStore($store);
        $request->session()->put('oldCartItems', $this->cartService->getItems());
        /** @var Offer $offer */
        foreach (Offer::query()->where('store_id', $store->id)->whereIn('product_id', $this->cartService->getItems()->pluck('product_id'))->get() as $offer) {
            $quantity = min($this->cartService->getItem($offer->product_id)->quantity, $offer->quantity);
            $this->cartService->set($offer->product_id, $quantity);

            if (!$request->session()->get('prescription', false))
                $request->session()->put('prescription', $offer->product->isPrescription());
        }

        if ($request->session()->get('prescription', false))
            $request->session()->flash('status', 'Заказать рецептурный препарат на сайте, можно только путем самовывоза из аптеки при наличии рецепта, выписанного врачом!');

        $cartService = $this->cartService;

        return view('checkout.index', compact('title', 'city', 'store', 'cartService'));
    }

    public function checkout(CheckoutRequest $request): RedirectResponse
    {
        if ($request->only(['delivery', 'payment']) and $request->filled('rule')) {
            $order = $this->orderService->checkout($request);

            foreach ($request->session()->get('oldCartItems', new Collection()) as $item) {
                try {
                    $newItem = $this->cartService->getItem($item->product_id);

                    $quantity = $item->quantity - $newItem->quantity;
                    if ($quantity > 0)
                        $this->cartService->set($item->product_id, $quantity);
                    else
                        $this->cartService->remove($item->product_id);
                }
                catch (\DomainException $exception) {}
            }

            if ((int)$request['payment'] === Order::PAYMENT_TYPE_SBERBANK) {
                $url = $this->orderService->paymentSberbank($order, route('checkout.finish', $order, true));
                return redirect($url);
            }

            return redirect()->route('checkout.finish', $order);
        }

        return back();
    }

    public function finish(Request $request, Order $order): View
    {
        $title = $this->title . ' | Заказ оформлен!';
        $city = $request->cookie('city', $this->defaultCity);
        $cartService = $this->cartService;

        return view('checkout.finish', compact('title', 'city', 'order', 'cartService'));
    }
}
