@extends('layouts.default')

@section('banner', '')

@section('content')
    <h1 class="text-center">Оформление заказа</h1>
    <div class="row checkout">
        <form method="post">
            @csrf
            <div class="col-8" style="border: 2px solid #f7f7f7; padding: 1.5rem">
                <h4 class="text-center">Способ получения</h4>
                <div class="row">
                    <div class="col-4 offset-1">
                        <label class="radio-button">
                            <input type="radio" class="radio-button_pin" />
                            <p class="radio-button_text">Самовывоз<span>Бесплатно</span></p>
                        </label>
                    </div>
                    <div class="col-5 offset-1">
                        Вы можете совершить покупку и забрать свой заказ самостоятельно, приехав в аптеку.
                    </div>
                </div>
                <div class="row">
                    <div class="col-5 offset-1"></div>
                    <div class="col-5">
                        Доставка осуществляется с 9:00 до 21:00, без выходных. По другим городам доставка осуществляется по таксометру.
                    </div>
                </div>
            </div>
            <div class="col-4" style="border: 2px solid #f7f7f7; padding: 1.5rem"></div>
        </form>
    </div>
@endsection
