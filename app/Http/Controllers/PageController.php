<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        $title = $this->title . ' | О компании';
        $cartService = $this->cartService;
        return view('pages.about', compact('title', 'cartService'));
    }

    public function advantage(): View
    {
        $title = $this->title . ' | Преимущества наших аптек';
        $cartService = $this->cartService;
        return view('pages.advantages', compact('title', 'cartService'));
    }

    public function deliveryBooking(): View
    {
        $title = $this->title . ' | Доставка/Бронирование';
        $cartService = $this->cartService;
        return view('pages.delivery-booking', compact('title', 'cartService'));
    }

    public function orderPayment(): View
    {
        $title = $this->title . ' | Оформление заказа';
        $cartService = $this->cartService;
        return view('pages.order-payment', compact('title', 'cartService'));
    }

    public function processingPersonalData(): View
    {
        $title = $this->title . ' | Обработка персональных данных';
        $cartService = $this->cartService;
        return view('pages.processing-personal-data', compact('title', 'cartService'));
    }

    public function privacyPolicy(): View
    {
        $title = $this->title . ' | Политика конфиденциальности';
        $cartService = $this->cartService;
        return view('pages.privacy-policy', compact('title', 'cartService'));
    }

    public function rent(): View
    {
        $title = $this->title . ' | Развитие сети/Аренда';
        $cartService = $this->cartService;
        return view('pages.rent', compact('title', 'cartService'));
    }

    public function return(): View
    {
        $title = $this->title . ' | Возврат';
        $cartService = $this->cartService;
        return view('pages.return', compact('title', 'cartService'));
    }

    public function rulesRemotely(): View
    {
        $title = $this->title . ' | Правила дистанционной торговли ЛС';
        $cartService = $this->cartService;
        return view('pages.rules-remotely', compact('title', 'cartService'));
    }
}
