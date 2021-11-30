<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        $title = $this->title . ' | О компании';
        $cartItems = $this->cartService->getItems();
        return view('pages.about', compact('title', 'cartItems'));
    }

    public function advantage(): View
    {
        $title = $this->title . ' | Преимущества наших аптек';
        $cartItems = $this->cartService->getItems();
        return view('pages.advantages', compact('title', 'cartItems'));
    }

    public function deliveryBooking(): View
    {
        $title = $this->title . ' | Доставка/Бронирование';
        $cartItems = $this->cartService->getItems();
        return view('pages.delivery-booking', compact('title', 'cartItems'));
    }

    public function orderPayment(): View
    {
        $title = $this->title . ' | Оформление заказа';
        $cartItems = $this->cartService->getItems();
        return view('pages.order-payment', compact('title', 'cartItems'));
    }

    public function processingPersonalData(): View
    {
        $title = $this->title . ' | Обработка персональных данных';
        $cartItems = $this->cartService->getItems();
        return view('pages.processing-personal-data', compact('title', 'cartItems'));
    }

    public function privacyPolicy(): View
    {
        $title = $this->title . ' | Политика конфиденциальности';
        $cartItems = $this->cartService->getItems();
        return view('pages.privacy-policy', compact('title', 'cartItems'));
    }

    public function rent(): View
    {
        $title = $this->title . ' | Развитие сети/Аренда';
        $cartItems = $this->cartService->getItems();
        return view('pages.rent', compact('title', 'cartItems'));
    }

    public function return(): View
    {
        $title = $this->title . ' | Возврат';
        $cartItems = $this->cartService->getItems();
        return view('pages.return', compact('title', 'cartItems'));
    }

    public function rulesRemotely(): View
    {
        $title = $this->title . ' | Правила дистанционной торговли ЛС';
        $cartItems = $this->cartService->getItems();
        return view('pages.rules-remotely', compact('title', 'cartItems'));
    }
}
