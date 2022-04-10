@extends('layouts.default')

@section('banner', '')

@section('content')
    {{ \Diglactic\Breadcrumbs\Breadcrumbs::render('checkout', $store) }}

    <h1 class="text-center">Оформление заказа</h1>
    <form method="post" class="row row-cols-1 checkout">
        @csrf
        <input type="hidden" name="store" value="{{ $store->id }}">
        <div class="col-md-8 p-4" style="border: 2px solid #f7f7f7;">
            <h4 class="text-center">Способ получения</h4>
            <div class="row">
                <div class="col-10 col-lg-6 col-xl-5 col-xxl-4 offset-1 offset-lg-0 offset-xl-1">
                    <label class="radio-button active">
                        <input type="radio" name="delivery" class="radio-button_pin" value="0" checked />
                        <p class="radio-button_text">Самовывоз<span>Бесплатно</span></p>
                    </label>
                </div>
                <div class="col-10 col-lg-6 col-xl-5 col-xxl-5 offset-1 offset-lg-0 offset-xl-1">
                    Вы можете совершить покупку и забрать свой заказ самостоятельно, приехав в аптеку.
                </div>
            </div>
            <div class="row">
                <div class="col-10 col-lg-6 col-xl-5 col-xxl-4 offset-1 offset-lg-0 offset-xl-1">
                    <label class="radio-button">
                        <input type="radio" name="delivery" class="radio-button_pin" value="0" disabled />
                        <p class="radio-button_text">Доставка<span>Указать адрес доставки.</span></p>
                    </label>
                </div>
                <div class="col-10 col-lg-6 col-xl-5 col-xxl-5 offset-1 offset-lg-0 offset-xl-1">
                    Доставка осуществляется с 9:00 до 21:00, без выходных. Доставка осуществляется по тарифам такси.
                </div>
            </div>

            <h4 class="text-center p-4 mt-3" style="border-top: 2px solid #f7f7f7;">Способ оплаты</h4>
            <div class="row">
                <div class="col-10 col-lg-5 col-xl-4 offset-1 offset-lg-0 offset-xl-1">
                    <label class="radio-button active">
                        <input type="radio" name="payment" class="radio-button_pin" value="0" checked />
                        <p class="radio-button_text">Оплата наличными<span>При получении</span></p>
                    </label>
                </div>
                <div class="col-10 col-lg-5 col-xl-4 offset-1 offset-lg-0 offset-lg-2">
                    <label class="radio-button">
                        <input type="radio" name="payment" class="radio-button_pin" value="1" />
                        <p class="radio-button_text">Оплата картой<span><img style="height: 20px" src="/images/payments.png" alt=""></span></p>
                    </label>
                </div>
            </div>

            <div class="row my-3">
                <div class="col offset-1 offset-lg-0 offset-xl-1">
                    <div class="form-check">
                        <input class="form-check-input{{ $errors->has('rule') ? ' is-invalid' : '' }}" type="checkbox" name="rule" id="rule">
                        <label class="form-check-label" for="rule">Я согласен(а) с правилами сайта</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 p-4" style="border: 2px solid #f7f7f7;">
            <h4 class="text-center">Ваш заказ</h4>
            @foreach($cartService->getItems() as $item)
                <div class="row">
                    <div class="col-6 col-md-8 offset-1 offset-md-0">
                        <a href="{{ route('catalog.product', ['product' => $item->product->slug]) }}">{{ $item->product->name }}</a>
                    </div>
                    <div class="col-4 col-md-4 text-end">
                        {{ $item->product->getPrice($store) }}&#8381;<br />
                        <span class="text-muted">x {{ $item->quantity }}</span>
                    </div>
                </div>
            @endforeach

            <div class="row py-3 mt-3" style="border-top: 2px solid #f7f7f7;">
                <div class="col-5 col-md-6 col-lg-8 offset-1 offset-md-0">Стоимость:</div>
                <div class="col-5 col-md-6 col-lg-4 text-end">{{ $cartService->getAmount() }}&#8381;</div>
            </div>
            <div class="row">
                <div class="col-5 col-md-6 col-lg-8 offset-1 offset-md-0">Самовывоз:</div>
                <div class="col-5 col-md-6 col-lg-4 text-end">Бесплатно</div>
            </div>

            <div class="row py-3 mt-3" style="border-top: 2px solid #f7f7f7;">
                <h5 class="col-5 col-md-6 col-lg-8 offset-1 offset-md-0">Итого:</h5>
                <h5 class="col-5 col-md-6 col-lg-4 text-end">{{ $cartService->getAmount() }}&#8381;</h5>
            </div>

            <div class="text-center">
                <button class="btn btn-primary" type="submit">Подтвердить заказ</button>
            </div>
        </div>
    </form>
@endsection
