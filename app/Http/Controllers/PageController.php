<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        $title = $this->title . ' | О компании';
        return view('pages.about', compact('title'));
    }

    public function advantage(): View
    {
        $title = $this->title . ' | Преимущества наших аптек';
        return view('pages.advantages', compact('title'));
    }

    public function deliveryBooking(): View
    {
        $title = $this->title . ' | Доставка/Бронирование';
        return view('pages.delivery-booking', compact('title'));
    }

    public function orderPayment(): View
    {
        $title = $this->title . ' | Оформление заказа';
        return view('pages.order-payment', compact('title'));
    }

    public function processingPersonalData(): View
    {
        $title = $this->title . ' | Обработка персональных данных';
        return view('pages.processing-personal-data', compact('title'));
    }

    public function privacyPolicy(): View
    {
        $title = $this->title . ' | Политика конфиденциальности';
        return view('pages.privacy-policy', compact('title'));
    }

    public function rent(): View
    {
        $title = $this->title . ' | Развитие сети/Аренда';
        return view('pages.rent', compact('title'));
    }

    public function return(): View
    {
        $title = $this->title . ' | Возврат';
        return view('pages.return', compact('title'));
    }

    public function rulesRemotely(): View
    {
        $title = $this->title . ' | Правила дистанционной торговли ЛС';
        return view('pages.rules-remotely', compact('title'));
    }
}
