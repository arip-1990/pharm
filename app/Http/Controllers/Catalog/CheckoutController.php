<?php

namespace App\Http\Controllers\Catalog;

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
        if (!$this->cartService->getAmount()) return redirect()->route('cart');

        return view('catalog.checkout', compact('title', 'city'));
    }
}
