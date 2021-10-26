@extends('layouts.default')

@section('banner', '')

@section('content')
    <h3 class="text-center">Доставка / Бронирование</h3>

    <div class="page">
        <div class="row">
            <div class="col-6 text-center">
                <img src="/images/content/box.svg" height="60px">
                <p class="fw-bold">Самовывоз</p>
                <p>
                    Вы можете совершить покупку и забрать свой заказ самостоятельно, приехав в аптеку.<br />
                    Оплата при получении наличными или картой.
                </p>
            </div>
            <div class="col-6 text-center">
                <img src="/images/content/delivery.svg" height="60px">
                <p class="fw-bold">Доставка</p>
                <p>
                    Доставка осуществляется с 9:00 до 21:00, без выходных.<br />
                    По другим городам доставка осуществляется по таксометру.<br />
                    Стоимость доставки по Махачкале и Каспийску 150 рублей.<br />
                </p>
            </div>
            <div class="col-12 mt-3">
                <p>
                    Согласно Указу Президента №187 от 17 марта 2020 года о дистанционной продажи безрецептурных лекарств осуществляется доставка на дом безрецептурных лекарственных средств, а также БАД, медицинских изделий, товаров для дома и красоты, бытовой химии и сопутствующих товаров.
                </p>
                <p>Заказать рецептурный препарат на сайте, можно только путем самовывоза из аптеки при наличии рецепта, выписанного врачом</p>
                <p>Информация о товаре, в том числе цена товара, носит ознакомительный характер и не является публичной офертой согласно ст.437 ГК РФ.</p>
            </div>
        </div>

        <div class="delivery-box">
            <div class="delivery-item">
                <img src="/images/delivery/i-delivery-0.png" alt="">
                <span>Мы работаем для вас без выходных!</span>
            </div>
            <div class="delivery-item">
                <img src="/images/delivery/i-delivery-1.png" alt="">
                <span>Доставка лекарств из ближайшей аптеки</span>
            </div>
            <div class="delivery-item">
                <img src="/images/delivery/i-delivery-2.png" alt="">
                <span>Бережная транспортировка надлежащих условиях</span>
            </div>
            <div class="delivery-item">
                <img src="/images/delivery/i-delivery-3.png" alt="">
                <span>Звонок курьера перед доставкой</span>
            </div>
            <div class="delivery-item">
                <img src="/images/delivery/i-delivery-4.png" alt="">
                <span>Доставка в удобный интервал времени</span>
            </div>
        </div>
        <p class="text-center">Оставайтесь дома! Заказывайте доставку! А мы бережно привезем все самое необходимое в удобное для вас время.</p>
    </div>
@endsection
