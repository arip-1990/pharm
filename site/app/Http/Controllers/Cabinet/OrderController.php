<?php

namespace App\Http\Controllers\Cabinet;

use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cabinet\EditProfileRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(): View
    {
        $title = ' | Заказы';

        $user = Auth::user();
        $cartService = $this->cartService;
        $orders = $user->orders;

        return view('cabinet.order.index', compact('title', 'user', 'orders', 'cartService'));
    }

    public function show(Order $order): View
    {
        $title = ' | Заказ №' . $order->id;

        $user = Auth::user();
        $cartService = $this->cartService;

        return view('cabinet.order.show', compact('title', 'user', 'order', 'cartService'));
    }
}
