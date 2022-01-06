@extends('layouts.default')

@section('banner', '')

@section('content')
    <div class="row">
        <aside class="col-3 sidebar">
            @include('layouts.partials.sidebar')
        </aside>

        <div class="col-9">
            <div class="card">
                <div class="card-header">Заказы</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($orders as $order)
                            <li class="list-group-item">
                                <h6 style="margin: 0"><a href="{{ route('profile.order.show', $order) }}">Заказ №{{ $order->id }}</a></h6>
                                <span class="text-secondary">Сумма заказа: {{ $order->getTotalCost() }}&#8381;</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
