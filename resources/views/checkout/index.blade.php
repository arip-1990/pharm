@extends('layouts.default')

@section('banner', '')

@section('content')
    <h1 class="text-center">Оформление заказа</h1>
    <form method="post" class="row checkout">
        @csrf
        <input type="hidden" name="store" value="{{ $store->id }}">
        <div class="col-8 p-4" style="border: 2px solid #f7f7f7;">
            <h4 class="text-center">Способ получения</h4>
            <div class="row">
                <div class="col-4 offset-1">
                    <label class="radio-button">
                        <input type="radio" name="delivery" class="radio-button_pin" value="0" checked />
                        <p class="radio-button_text">Самовывоз<span>Бесплатно</span></p>
                    </label>
                </div>
                <div class="col-5 offset-1">
                    Вы можете совершить покупку и забрать свой заказ самостоятельно, приехав в аптеку.
                </div>
            </div>
            <div class="row">
                <div class="col-4 offset-1">
                    <label class="radio-button">
                        <input type="radio" name="delivery" class="radio-button_pin" value="0" disabled />
                        <p class="radio-button_text">Доставка<span>Стоимость доставки 150 руб.</span></p>
                    </label>
                </div>
                <div class="col-5 offset-1">
                    Доставка осуществляется с 9:00 до 21:00, без выходных. По другим городам доставка осуществляется по таксометру.
                </div>
            </div>

            <h4 class="text-center p-4 mt-3" style="border-top: 2px solid #f7f7f7;">Способ оплаты</h4>
            <div class="row">
                <div class="col-4 offset-1">
                    <label class="radio-button">
                        <input type="radio" name="payment" class="radio-button_pin" value="0" checked />
                        <p class="radio-button_text">Оплата наличными<span>При получении</span></p>
                    </label>
                </div>
                <div class="col-4 offset-1">
                    <label class="radio-button">
                        <input type="radio" name="payment" class="radio-button_pin" value="1" />
                        <p class="radio-button_text">Оплата картой<span><img style="height: 20px" src="/images/payments.png" alt=""></span></p>
                    </label>
                </div>
            </div>

            <div class="row my-3">
                <div class="col offset-1">
                    <div class="form-check">
                        <input class="form-check-input{{ $errors->has('rule') ? ' is-invalid' : '' }}" type="checkbox" name="rule" id="rule">
                        <label class="form-check-label" for="rule">Я согласен(а) с правилами сайта</label>
                        <small class="text-danger">{{ $errors->first('rule') }}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4 border-start-0 p-4" style="border: 2px solid #f7f7f7;">
            <h4 class="text-center">Ваш заказ</h4>
            @foreach($cartService->getItems() as $item)
                <div class="row">
                    <div class="col-8">
                        {{ $item->product->name }}
                    </div>
                    <div class="col-4 text-end">
                        {{ $item->product->getPrice($store) }}&#8381;<br />
                        <span class="text-muted">x {{ $item->quantity }}</span>
                    </div>
                </div>
            @endforeach

            <div class="row py-3 mt-3" style="border-top: 2px solid #f7f7f7;">
                <div class="col-8">Стоимость:</div>
                <div class="col-4 text-end">{{ $cartService->getAmount() }}&#8381;</div>
            </div>
            <div class="row">
                <div class="col-8">Самовывоз:</div>
                <div class="col-4 text-end">Бесплатно</div>
            </div>

            <div class="row py-3 mt-3" style="border-top: 2px solid #f7f7f7;">
                <h5 class="col-8">Итого:</h5>
                <h5 class="col-4 text-end">{{ $cartService->getAmount() }}&#8381;</h5>
            </div>

            <div class="text-center">
                <button class="btn btn-primary" type="submit">Подтвердить заказ</button>
            </div>
        </div>
    </form>
@endsection
