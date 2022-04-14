<?php

namespace App\Http\Controllers\Catalog;

use App\Models\CartItem;
use App\Models\Offer;
use App\Models\Order;
use App\Models\ProductStatistic;
use App\Models\Status;
use App\Models\Store;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\CheckoutRequest;
use App\UseCases\Order\CheckoutService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CheckoutController extends Controller
{
    public function __construct(private CheckoutService $orderService)
    {
        parent::__construct();
    }

    public function index(Request $request, Store $store): View | RedirectResponse
    {
        $title = ' | Состав заказа';
        if (!$this->cartService->getItems()->count()) return redirect()->route('cart');

        $this->cartService->setStore($store);
        $items = new Collection();
        /** @var Offer $offer */
        foreach (Offer::query()->where('store_id', $store->id)->whereIn('product_id', $this->cartService->getItems()->pluck('product_id'))->get() as $offer) {
            $item = $this->cartService->getItem($offer->product_id);
            $quantity = min($item->quantity, $offer->quantity);
            if ($quantity > 0)
                $items->add(new CartItem([
                    'product_id' => $offer->product_id,
                    'quantity' => $quantity
                ]));

            if (!$request->session()->get('recipe', false))
                $request->session()->put('recipe', $offer->product->recipe);
        }

        $this->cartService->clear();
        $this->cartService->setItems($items);

        if ($request->session()->get('recipe', false))
            $request->session()->flash('status', 'Заказать рецептурный препарат на сайте, можно только путем самовывоза из аптеки при наличии рецепта, выписанного врачом!');

        $cartService = $this->cartService;

        return view('checkout.index', compact('title', 'store', 'cartService'));
    }

    public function checkout(CheckoutRequest $request): RedirectResponse
    {
        $order = $this->orderService->checkout($request);

        if ((int)$request['payment'] === Order::PAYMENT_TYPE_SBER) {
            return redirect($this->orderService->paymentSberbank($order, route('checkout.finish', $order, true)));
        }

        return redirect()->route('checkout.finish', $order);
    }

    public function finish(Order $order): View
    {
        $title = ' | Заказ оформлен!';
        $cartService = $this->cartService;

        try {
            foreach ($order->items as $item) {
                $statistic = ProductStatistic::query()->find($item->product_id);
                if ($statistic) $statistic->increment('orders', $item->quantity);
                else ProductStatistic::query()->create(['id' => $item->product_id, 'orders' => $item->quantity]);
            }
        }
        catch (\Exception $e) {}

        if (!$order->inStatus(Status::STATUS_PAID) and !$order->isSend()) {
            $order->sent();
            $order->save();
        }

        return view('checkout.finish', compact('title', 'order', 'cartService'));
    }
}
