<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $exception->getStatusCode() }}</title>

        <link href="{{ mix('/css/app.css') }}" rel="stylesheet">
    </head>
    <body>
        <header class="container my-3">
            <div class="row">
                <div class="col-5 menu-city">
                    <div>
                        <span>Ваш город:</span>
                        <span>{{ config('data.city')[0] }}</span>
                    </div>
                </div>

                <div class="auth col-7 text-end">
                    <span class="phone">+7 (8722) 66-06-05</span>
                    <span class="d-inline-block">
                        @auth
                            <a href="{{ route('profile') }}">Личный кабинет</a> |
                            <a href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Выйти</a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        @else
                            <a href="{{ route('login') }}" data-toggle="modal" data-target="login">Вход</a> |
                            <a href="{{ route('register') }}" data-toggle="modal"  data-target="register">Регистрация</a>
                        @endauth
                    </span>
                </div>
            </div>

            <div class="row fixed-box">
                <div class="col-6 col-md-4 col-lg-3 me-auto me-lg-0">
                    <a href="{{ route('home') }}">
                        <img src="/images/logo.svg" alt="logo" class="logo">
                    </a>
                </div>
                <div class="col-12 col-lg-7 order-3 order-lg-0 mt-3 mt-lg-0">
                    <form class="search" action="{{ route('catalog.search') }}" autocomplete="off">
                        <input type="search" name="q" class="form-control" placeholder="Введите: название препарата, производителя, действующее вещество" />
                        <button class="btn btn-primary btn-search">Найти</button>
                    </form>
                </div>
                <div class="col-3 col-sm-2 col-lg-1 text-center" style="margin-top: 19px;">
                    <a class="fav" href="{{ route('favorite') }}">
                        <span class="quantity">{{ count(session('favorites', [])) }}</span>
                        <img src="/images/heart.png" style="height: 30px;">
                        Избранное
                    </a>
                </div>
                <div class="col-3 col-sm-2 col-lg-1 text-center" style="margin-top: 19px;">
                    <a class="cart" href="{{ route('cart') }}">
                        @php
                            $totalQuantity = 0;
                            foreach (session('cartItems', []) as $item)
                                $totalQuantity += $item->quantity;
                        @endphp
                        <span class="quantity">{{ $totalQuantity }}</span>
                        <img src="/images/cart.png" style="height: 30px;">
                        Корзина
                    </a>
                </div>
            </div>

            <div class="row">
                <nav class="navbar navbar-expand-md navbar-primary">
                    <button class="navbar-toggler" type="button" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false">
                        <i class="fas fa-bars"></i>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarCollapse">
                        <ul class="navbar-nav my-lg-0">
                            <li class="nav-item">
                                <a class="nav-link active" href="{{ url('/catalog') }}">
                                    <i class="fas fa-bars"></i> Наш ассортимент
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('pharmacy') }}">
                                    <i class="far fa-hospital"></i> Аптеки
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('deliveryBooking') }}">
                                    <i class="fas fa-ambulance"></i> Доставка/бронирование
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </header>

        <main class="container my-3">
            @yield('content')
        </main>

        <footer class="container">
            <div class="row ofer">
                <span>Информация о товаре, в том числе цена товара, носит ознакомительный характер и не является публичной офертой согласно ст.437 ГК РФ.</span>
            </div>

            <div class="row footer">
                <div class="col-sm-10 col-md-6 offset-sm-1 offset-md-0 ps-md-5 d-flex align-items-center">
                    <img class="logo" src="/images/logo_min.svg" alt="logo">
                    <div class="info-phone text-center">
                        <h5>Единая справочная сети</h5>
                        <p>+7 (8722) 606-366</p>
                        <p class="times">ежедневно с 9:00 до 21:00</p>
                    </div>
                </div>
                <div class="col-10 col-sm-5 col-md-3 offset-1 offset-md-0">
                    <ul>
                        <li><h4><a href="{{ route('about') }}">О компании</a></h4></li>
                        <li><a href="{{ route('advantage') }}">Наши преимущества</a></li>
                        <li><a href="{{ route('pharmacy') }}">Адреса аптек</a></li>
                        <li><a href="{{ route('rent') }}">Развитие сети/Аренда</a></li>
                        <li><a href="{{ route('rulesRemotely') }}">Правила дистанционной торговли ЛС</a></li>
                        <li><a href="{{ route('return') }}">Условия возврата</a></li>
                    </ul>
                </div>
                <div class="col-10 col-sm-5 col-md-3 offset-1 offset-sm-0 mt-3 mt-sm-0">
                    <ul>
                        <li><h4><a href="{{ route('profile') }}">Личный кабинет</a></h4></li>
                        <li><a href="{{ route('register') }}" data-toggle="modal"  data-target="register">Регистрация</a></li>
                        <li><a href="{{ route('favorite') }}">Отложенные товары</a></li>
                        <li><a href="{{ route('processingPersonalData') }}">Обработка персональных данных</a></li>
                        <li><a href="{{ route('privacyPolicy') }}">Политика конфиденциальности</a></li>
                        <li><a href="{{ route('orderPayment') }}">Оплата заказа</a></li>
                    </ul>
                </div>
                <div class="col-9 mt-3 mt-md-0">
                    <p style="font-size: .75rem;margin: 0;">
                        ООО «Социальная аптека»;<br>
                        Адрес: Республика Дагестан, г. Махачкала, пр. Гамидова, дом 48; <br>
                        Лицензия: № ЛО-05-02-001420 от 27 декабря 2019 г.; <br>
                        ИНН 0571008484; ОГРН: 1160571061353
                    </p>
                </div>
                <p class="col-3 text-end align-self-end m-0" style="font-size: .8rem; letter-spacing: 2px;">&copy;{{ date('Y') }}</p>
            </div>

            <div class="row">
                <div class="text-center box-warning">
                    ИМЕЮТСЯ ПРОТИВОПОКАЗАНИЯ. НЕОБХОДИМА КОНСУЛЬТАЦИЯ СПЕЦИАЛИСТА.
                </div>
            </div>
        </footer>

        <script src="{{ mix('/js/app.js') }}"></script>
        @yield('scripts')
    </body>
</html>
