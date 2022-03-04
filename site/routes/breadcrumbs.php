<?php

use Diglactic\Breadcrumbs\Breadcrumbs;

// This import is also not required, and you could replace `BreadcrumbTrail $trail`
//  with `$trail`. This is nice for IDE type checking and completion.
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Home
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Главная', route('home'));
});

// Pages
Breadcrumbs::for('about', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('О компании', route('about'));
});
Breadcrumbs::for('advantage', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Преимущества наших аптек', route('advantage'));
});
Breadcrumbs::for('deliveryBooking', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Доставка/Бронирование', route('deliveryBooking'));
});
Breadcrumbs::for('orderPayment', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Оформление заказа', route('orderPayment'));
});
Breadcrumbs::for('processingPersonalData', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Обработка персональных данных', route('processingPersonalData'));
});
Breadcrumbs::for('privacyPolicy', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Политика конфиденциальности', route('privacyPolicy'));
});
Breadcrumbs::for('rent', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Развитие сети/Аренда', route('rent'));
});
Breadcrumbs::for('return', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Возврат', route('return'));
});
Breadcrumbs::for('rulesRemotely', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Правила дистанционной торговли ЛС', route('rulesRemotely'));
});

// Cabinet
Breadcrumbs::for('profile', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Профиль', route('profile'));
});
Breadcrumbs::for('order', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Заказы', route('order'));
});
Breadcrumbs::for('orderShow', function (BreadcrumbTrail $trail, \App\Entities\Order $order) {
    $trail->parent('order');
    $trail->push('Заказ №' . $order->id, route('order.show', $order));
});
Breadcrumbs::for('favorite', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Избранное', route('favorite'));
});

// Pharmacy
Breadcrumbs::for('pharmacy', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Точки самовывоза', route('pharmacy'));
});
Breadcrumbs::for('pharmacyShow', function (BreadcrumbTrail $trail, \App\Entities\Store $store) {
    $trail->parent('pharmacy');
    $trail->push($store->name, route('pharmacy.show', $store));
});

// Cart
Breadcrumbs::for('cart', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Корзина', route('cart'));
});
Breadcrumbs::for('cartPharmacy', function (BreadcrumbTrail $trail) {
    $trail->parent('cart');
    $trail->push('Выбор аптеки', route('cart.pharmacy'));
});
Breadcrumbs::for('checkout', function (BreadcrumbTrail $trail) {
    $trail->parent('cartPharmacy');
    $trail->push('Оформление заказа');
});

// Catalog
Breadcrumbs::for('category', function (BreadcrumbTrail $trail, \App\Entities\Category $category) {
    if ($category->parent) $trail->parent('category', $category->parent);
    $trail->push($category->name, route('catalog', $category));
});
Breadcrumbs::for('catalog', function (BreadcrumbTrail $trail, \App\Entities\Category $category = null) {
    $trail->parent('home');
    $trail->push('Наш ассортимент', route('catalog'));

    if ($category) $trail->parent('category', $category);
});
Breadcrumbs::for('catalogProduct', function (BreadcrumbTrail $trail, \App\Entities\Product $product) {
    $trail->parent('catalog', $product->category);
    $trail->push($product->name, route('catalog.product', $product));
});
Breadcrumbs::for('catalogSale', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Наш ассортимент', route('catalog'));
    $trail->push('Распродажа', route('catalog.sale'));
});
