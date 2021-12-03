<?php

namespace App\Http\Controllers\Catalog;

use App\Entities\CartItem;
use App\Entities\Offer;
use App\Entities\Store;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\CheckoutRequest;
use App\UseCases\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
        $productIds = [];
        /** @var CartItem $item */
        foreach ($this->cartService->getItems() as $item)
            $productIds[$item->product_id] = $item->quantity;

//        $request->session()->put('oldCartItems', $this->cartService->getItems());
        $this->cartService->clear();
        /** @var Offer $offer */
        foreach (Offer::query()->where('store_id', $store->id)->whereIn('product_id', array_keys($productIds))->get() as $offer) {
            $quantity = min($productIds[$offer->product_id], $offer->quantity);
            $this->cartService->add(CartItem::create($offer->product_id, $quantity));

            if (!$request->session()->get('prescription', false))
                $request->session()->put('prescription', $offer->product->isPrescription());
        }

        if ($request->session()->get('prescription', false))
            $request->session()->flash('status', 'Заказать рецептурный препарат на сайте, можно только путем самовывоза из аптеки при наличии рецепта, выписанного врачом!');

        $cartService = $this->cartService;

        return view('catalog.checkout', compact('title', 'city', 'store', 'cartService'));
    }

    public function checkout(CheckoutRequest $request): RedirectResponse
    {
        if ($request->only(['delivery', 'payment']) and $request->filled('rule')) {
            $order = $this->orderService->checkout($request);
            dd($order);
        }

        return back()->with('error', 'Возникла ошибка при оформлении заказа!');
    }
}
