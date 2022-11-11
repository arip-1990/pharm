<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ env('APP_NAME') }}</title>
    @yield('meta')
</head>
<body>
    <div style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22px; background-color: #eee; padding: 3rem 0; margin: auto;">
        <div style="max-width: 640px; margin: auto; box-sizing: border-box; display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <div style="width: 120px;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 62 62">
                    <g>
                        <circle cx="31.03" cy="31.03" r="30.44" style="fill:#fff"></circle>
                        <path d="M31 1.61a29.42 29.42 0 0 1 20.8 50.22 29.42 29.42 0 1 1-41.6-41.6A29.21 29.21 0 0 1 31 1.61M31 0a31 31 0 1 0 31 31A31 31 0 0 0 31 0Z" style="fill:#1dab9e"></path>
                        <path d="M31.05 17.79A12.74 12.74 0 0 1 36.94 13c4.83-1.72 9.42-1.24 13.47 2s5.38 7.87 4.76 13c-.84 6.89-4.56 12.59-9.09 17.32C42 49.71 37.4 53.51 32 55.85a2 2 0 0 1-1.72.18c-5.07-1.8-13.56-8.89-18.52-15.55a24.24 24.24 0 0 1-5.02-14.62c0-6.09 3.48-11.22 8.77-13a13.08 13.08 0 0 1 15.17 4.6 4.18 4.18 0 0 0 .37.33Z" style="fill:#d63517"></path>
                        <path d="M31.56 48.65h-.06a.41.41 0 0 1-.34-.41V25.16l-3.1 11.38a.4.4 0 0 1-.75.08L25.52 33l-.72 1.68a.4.4 0 0 1-.37.24h-11a.41.41 0 0 1 0-.81h10.73l1-2.24a.42.42 0 0 1 .37-.24.4.4 0 0 1 .37.22l1.67 3.43 3.63-13.72a.4.4 0 0 1 .43-.31.4.4 0 0 1 .36.4v24l3.18-11.3a.4.4 0 0 1 .39-.28h13a.41.41 0 0 1 0 .81H35.83L32 48.37a.42.42 0 0 1-.44.28Z" style="fill:#fff"></path>
                    </g>
                </svg>
{{--                <img style="width: 100%; height: 100%;" src="{{ $message->embed($pathToImage) }}"  alt="120на80.рф"/>--}}
            </div>
            <div>
                <a style="text-decoration: none; margin-right: .5rem;" href="https://120на80.рф/catalog">Каталог товаров</a>
                <a style="text-decoration: none;" href="https://120на80.рф/profile">Кабинет</a>
            </div>
        </div>
        <div style="max-width: 640px; margin: auto; box-sizing: border-box; border: 1px solid #ccc; padding: 1rem 0; background-color: #f6f6f6;">
            <div style="border-bottom: 2px solid #ccc; margin: 0 1rem">
                <h2>Здравствуйте, {{ $order->name }}!</h2>
                <p>
                    Спасибо за покупку! Ожидайте подтверждения заказа.<br />
                    Отслеживать его статус можно в
                    <a style="text-decoration: none;" href="https://120на80.рф/profile">личном кабинете</a>.
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
                        <span>{{ $order->store->location->getAddress() }}</span>
                    </li>
                    <!--				<li style="display: flex; justify-content: space-between; margin-bottom: 1rem;"><b>Дата получения заказа:</b><span>завтра после 12:30</span></li>-->
                    <li style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <b>Режим работы аптеки:</b>
                        <span>{{ \App\Helper::formatSchedule($order->store->schedule) }}</span>
                    </li>
                    <li style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <b>Способы оплаты:</b>
                        <span>{{ $order->payment->type === \App\Models\Payment::TYPE_CARD ? 'Банковской картой' : 'Наличными' }}</span>
                    </li>
                </ul>
            </div>
            <div style="padding: 1rem">
                <h2>Помощь по заказу</h2>
                <p>Свяжитесь с нашей службой поддержки любым удобным для вас способом:</p>
                <ul>
                    <li style="margin-bottom: 1rem;">по телефону: <a style="text-decoration: none;" href="tel:+78722606366">+7 (8722) 606-366</a></li>
                    <li style="margin-bottom: 1rem;">по почте: <a style="text-decoration: none;" href="mailto:{{ config('data.infoEmail') }}">{{ config('data.infoEmail') }}</a></li>
                </ul>
            </div>
        </div>
        <div style="max-width: 640px; margin: 0 auto; box-sizing: border-box; background-color: #f6f6f6; display: flex; justify-content: space-between; padding: 1rem;">
            <span>&copy; {{ env('APP_NAME') }}, {{ date('Y') }}</span>
        </div>
    </div>
</body>
</html>
