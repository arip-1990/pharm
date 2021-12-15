<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ getenv('APP_NAME') . ($title ?? '') }}</title>
        @yield('meta')

        <link href="{{ mix('/css/app.css') }}" rel="stylesheet">
    </head>
    <body>
        <header class="container my-3">
            <div class="row">
                <div class="col-5 menu-city">
                    <div>
                        <span>Ваш город:</span>
                        @php $city = Illuminate\Support\Facades\Cookie::get('city', config('data.city')[0]) @endphp
                        <a class="dropdown-toggle" href="#" role="button" aria-expanded="false">{{ $city }}</a>
                        <ul class="dropdown-menu p-0">
                            @foreach (config('data.city') as $item)
                                <li>
                                    <a class="dropdown-item{{ $city == $item ? ' active': '' }}" href="{{ route('setCity', ['city' => $item]) }}">{{ $item }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="city-choose" style="{{ (!Illuminate\Support\Facades\Cookie::has('city')) ? 'display: flex;' : '' }}">
                        <h5 class="w-100 mb-3">Ваш город {{ $city }}?</h5>
                        <a class="btn btn-sm btn-primary" href="{{ route('setCity', ['city' => $city]) }}">Да, все верно</a>
                        <button class="btn btn-sm btn-outline-secondary city-another">Выбрать другой</button>
                    </div>
                </div>

                <div class="auth col-7 text-end">
                    <span class="phone">+7 (8722) 60-63-66</span>
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

            <div class="empty-box"></div>
            <div class="fixed-box">
                <div class="row container align-items-center p-0 mx-auto">
                    <div class="col-6 col-md-4 col-lg-3 me-auto me-lg-0">
                        <a href="{{ route('home') }}">
                            <img src="/images/logo.svg" alt="logo" class="logo">
                        </a>
                    </div>
                    <div class="col-12 col-lg-7 order-3 order-lg-0 mt-3 mt-lg-0">
                        <form class="search" action="{{ route('catalog.search') }}" autocomplete="off">
                            <input type="search" name="q" class="form-control" placeholder="Введите: название препарата, производителя, действующее вещество" />
                            <button type="submit" class="btn btn-primary btn-search">Найти</button>
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
                            <span class="quantity">{{ $cartService->getTotal() }}</span>
                            <img src="/images/cart.png" style="height: 30px;">
                            Корзина
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <nav class="navbar navbar-expand-md navbar-primary">
                    <button class="navbar-toggler" type="button" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false">
                        <i class="fas fa-bars"></i>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarCollapse">
                        <ul class="navbar-nav my-lg-0">
                            @switch(\Illuminate\Support\Facades\Route::currentRouteName())
                                @case('catalog')
                                    <li class="nav-item sale">
                                        <span class="nav-link active">
                                            <i class="fas fa-bars"></i> Наш ассортимент
                                        </span>
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
                                    @break
                                @case('pharmacy')
                                    <li class="nav-item sale">
                                        <a class="nav-link" href="{{ route('catalog') }}">
                                            <i class="fas fa-bars"></i> Наш ассортимент
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <span class="nav-link active">
                                            <i class="far fa-hospital"></i> Аптеки
                                        </span>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('deliveryBooking') }}">
                                            <i class="fas fa-ambulance"></i> Доставка/бронирование
                                        </a>
                                    </li>
                                    @break
                                @case('deliveryBooking')
                                    <li class="nav-item sale">
                                        <a class="nav-link" href="{{ route('catalog') }}">
                                            <i class="fas fa-bars"></i> Наш ассортимент
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('pharmacy') }}">
                                            <i class="far fa-hospital"></i> Аптеки
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <span class="nav-link active">
                                            <i class="fas fa-ambulance"></i> Доставка/бронирование
                                        </span>
                                    </li>
                                    @break
                                @default
                                    <li class="nav-item sale">
                                        <a class="nav-link" href="{{ route('catalog') }}">
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
                            @endswitch
                        </ul>
                    </div>
                </nav>
            </div>
        </header>

        <section class="container-fluid">
            @section('banner')
                <div class="row">
                    <img src="/images/banner.jpg" alt="banner">
                </div>
            @show
        </section>

        <main class="container my-3">
            <div id="flash">
                @include('layouts.partials.flash')
            </div>

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
                        <li>
                            <h4>
                                @auth()
                                    <a href="{{ route('profile') }}">Личный кабинет</a>
                                @else
                                    <a href="{{ route('profile') }}" data-toggle="modal" data-target="login">Личный кабинет</a>
                                @endauth
                            </h4>
                        </li>
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

        <section id="modal">
            @include('layouts.partials.modals.login')
            @include('layouts.partials.modals.register')
            @include('layouts.partials.modals.product')
        </section>
        <div id="backdrop" class="modal-backdrop fade" style="display: none"></div>

        <script src="{{ mix('/js/app.js') }}"></script>
        @yield('scripts')
    </body>
</html>
