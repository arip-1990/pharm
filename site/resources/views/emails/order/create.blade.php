<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ getenv('APP_NAME') . ($title ?? '') }}</title>
        @yield('meta')
    </head>
    <body>
        <div style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22px; background-color: #eee; padding: 3rem 0; margin: auto;">
            <div style="max-width: 640px; margin: auto; box-sizing: border-box; display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <div style="width: 120px;"><img style="width: 100%; height: 100%;" src="{{ $message->embed($pathToImage) }}"  alt=""/></div>
                <div>
                    <a style="text-decoration: none; margin-right: .5rem;" href="https://120на80.рф/catalog">Каталог товаров</a>
                    <a style="text-decoration: none;" href="https://120на80.рф/cabinet/profile">Кабинет</a>
                </div>
            </div>
            <div style="max-width: 640px; margin: auto; box-sizing: border-box; border: 1px solid #ccc; padding: 1rem 0; background-color: #f6f6f6;">
                <div style="border-bottom: 2px solid #ccc; margin: 0 1rem">
                    <h2>Здравствуйте, {{ $order->user->name }}!</h2>
                    <p>
                        Спасибо за покупку! Ожидайте подтверждения заказа.<br />
                        Отслеживать его статус можно в
                        <a style="text-decoration: none;" href="https://120на80.рф/cabinet/order/view?id={{ $order->id }}">личном кабинете</a>.
                    </p>
                </div>
                <div style="padding: 1rem;">
                    <h2>Заказ №{{ config('data.orderStartNumber') + $order->id }}</h2>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        @foreach($order->items as $item)
                            <li style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                                {{ $item->product->name }}
                                <span>{{ $item->price }}x{{ $item->quantity }} руб.</span>
                            </li>
                        @endforeach
                        <li style="display: flex; margin-bottom: 1rem; justify-content: flex-end; font-size: 1.05rem;">
                            <b>{{ ($order->isPay() ? 'Оплачено: ' : 'Итого к оплате: ') . $order->getTotalCost() . 'руб.' }}</b>
                        </li>
                    </ul>
                </div>
                <div style="background-color: #e1e1e1; padding: 1rem;">
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <li style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                            <b>Адрес аптеки:</b>
                            <span>{{ $order->store->address }}</span>
                        </li>
                        <!--				<li style="display: flex; justify-content: space-between; margin-bottom: 1rem;"><b>Дата получения заказа:</b><span>завтра после 12:30</span></li>-->
                        <li style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                            <b>Режим работы аптеки:</b>
                            <span>{{ \App\Helper::formatSchedule($order->store->schedule) }}</span>
                        </li>
                        <li style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                            <b>Способы оплаты:</b>
                            <span>{{ $order->payment_type === \App\Entities\Order::PAYMENT_TYPE_SBERBANK ? 'Банковской картой' : 'Наличными' }}</span>
                        </li>
                    </ul>
                </div>
                <div style="padding: 1rem">
                    <h2>Помощь по заказу</h2>
                    <p>Свяжитесь с нашей службой поддержки любым удобным для вас способом:</p>
                    <ul>
                        <li style="margin-bottom: 1rem;">по телефону +7 (8722) 606-366</li>
                        <li style="margin-bottom: 1rem;">по почте <a style="text-decoration: none;" href="mailto:{{ config('data.infoEmail') }}">{{ config('data.infoEmail') }}</a></li>
                    </ul>
                </div>
            </div>
            <div style="max-width: 640px; margin: 0 auto; box-sizing: border-box; background-color: #f6f6f6; display: flex; justify-content: space-between; padding: 1rem;">
                <span>&copy; {{ env('APP_NAME') }}, {{ date('Y') }}</span>
            </div>
        </div>
    </body>
</html>
