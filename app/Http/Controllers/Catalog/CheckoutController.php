<?php

namespace App\Http\Controllers\Catalog;

use App\Entities\CartItem;
use App\Entities\Offer;
use App\Entities\Store;
use App\Http\Controllers\Controller;
use App\UseCases\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(private CartService $cartService)
    {
        parent::__construct();
    }

    public function index(Request $request, Store $store): View | RedirectResponse
    {
        $title = $this->title . ' | Состав заказа';
        $city = $request->cookie('city', $this->defaultCity);
        if (!$this->cartService->getItems()->count()) return redirect()->route('cart');

        $productsId = [];
        /** @var CartItem $item */
        foreach ($this->cartService->getItems() as $item)
            $productsId[$item->product_id] = $item->quantity;

        $this->cartService->clear();
        /** @var Offer $offer */
        foreach (Offer::query()->where('store_id', $store->id)->whereIn('product_id', array_keys($productsId))->get() as $offer) {
            $quantity = $productsId[$offer->product_id] < $offer->quantity ? $productsId[$offer->product_id] : $offer->quantity;
            $this->cartService->add(CartItem::create($offer->product_id, $quantity));

            if (!$request->session()->get('prescription', false))
                $request->session()->put('prescription', $offer->product->isPrescription());
        }

        if ($request->session()->get('prescription', false))
            $request->session()->flash('status', 'Заказать рецептурный препарат на сайте, можно только путем самовывоза из аптеки при наличии рецепта, выписанного врачом!');

        $this->cartService->setStore($store);
        $cartService = $this->cartService;

        return view('catalog.checkout', compact('title', 'city', 'store', 'cartService'));
    }
}
