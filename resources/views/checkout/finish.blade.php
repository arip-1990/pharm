@extends('layouts.default')

@section('banner', '')

@section('content')
    <h1 class="text-center">Спасибо, заказ №{{ $order->id + config('data.orderStartNumber') }} оформлен!</h1>

    <div>
        <p>Мы отправим электронное письмо с информацией о статусах вашего заказа на электронную почту (если вы ее указали).</p>
        <p>Когда заказ будет собран в аптеке, с вами свяжется оператор.</p>
        <p>В случае возникновения дополнительных вопросов о наличии товара или интервалов доставки с вами свяжется оператор.</p>
        <br>
        <p>Статус заказа можно проверить в личном кабинете или по телефону:</p>
        <b>+7(8722) 66-06-05</b>
    </div>
@endsection
