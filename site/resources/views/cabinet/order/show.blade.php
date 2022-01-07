@extends('layouts.default')

@section('banner', '')

@section('content')
    {{ \Diglactic\Breadcrumbs\Breadcrumbs::render('orderShow', $order) }}

    <div class="row">
        <aside class="col-3 sidebar">
            @include('layouts.partials.sidebar')
        </aside>

        <div class="col-9">
            <div class="card">
                <div class="card-header">Заказ №{{ $order->id  }} - {!! \App\Helper::getStatusInfo($order->status) !!}</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <h4>Личные данные</h4>
                            <p><b class="text-secondary">Имя: </b>{{ $user->name }}</p>
                            <p><b class="text-secondary">Телефон: </b>{{ \App\Helper::formatPhone($user->phone, true) }}</p>
                        </li>
                        <li class="list-group-item">
                            <h4>Точка самовывоза</h4>
                            <p><b class="text-secondary">Адрес самовывоза: </b>{{ $order->store->address }}</p>
                            <p><b class="text-secondary">Мобильный телефон: </b>{{ \App\Helper::formatPhone($order->store->phone) }}</p>
                            <p><b class="text-secondary">Время работы: </b>{!! \App\Helper::formatSchedule($order->store->schedule) !!}</p>
                            <p><b class="text-secondary">Способ оплаты: </b>{{ $order->payment_type === \App\Entities\Order::PAYMENT_TYPE_SBERBANK ? 'картой' : 'наличными' }}</p>
                            <p><b class="text-secondary">Сумма заказа: </b>{{ $order->getTotalCost() }}&#8381;</p>
                        </li>
                        <li class="list-group-item">
                            <h4>Товары</h4>
                            <div class="row row-cols-sm-3">
                                @foreach($order->items as $item)
                                    <div class="col" style="text-align: center">
                                        <p><a href="{{ route('catalog.product', $item->product) }}">
                                            @if ($item->product->photos()->count())
                                                <img alt="{{ $item->product->name }}" src="{{ url($item->product->photos()->first()->getOriginalFile()) }}" width="150" />
                                            @else
                                                <img alt="{{ $item->product->name }}" src="{{ url(\App\Entities\Photo::DEFAULT_FILE) }}" width="150" />
                                            @endif
                                            <br /><span>{{ $item->product->name }}</span>
                                        </a></p>
                                        <span class="text-muted">{{ $item->price }}&#8381; x {{ $item->quantity }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
