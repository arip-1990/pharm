@extends('layouts.default')

@section('banner', '')

@section('content')
    <div class="row">
        <div class="col-6"><span class="border-bottom border-primary">Выбор аптеки</span></div>
        <div class="col-2 text-center">Наличие</div>

        <div class="accordion" id="stores">
            @foreach($stores as $store)
                <div class="store-item">
                    <div class="store-item_title collapsed" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $store['store']->id }}">
                        <h5 class="col-6">{{ $store['store']->name }}</h5>
                        <p class="col-2 text-center text-primary">{{ count($store['products']) }} из {{ $total }} товаров</p>
                        <p class="col-2 text-end">
                            @php
                                $price = 0;
                                foreach ($store['products'] as $product)
                                    $price += $product['quantity'] * $product['price'];
                            @endphp
                            {{ $price }} р.
                        </p>
                        <p class="col-2 text-end">
                            <a href="{{ route('cart.checkout', ['store'=> $store['store']->id]) }}" class="btn btn-primary">Выбрать аптеку</a>
                        </p>
                    </div>
                    <div id="collapse-{{ $store['store']->id }}" class="collapse" data-bs-parent="#stores">
                        <div class="description-item_body">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
