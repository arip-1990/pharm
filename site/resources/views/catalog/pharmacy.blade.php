@extends('layouts.default')

@section('banner', '')

@section('content')
    {{ \Diglactic\Breadcrumbs\Breadcrumbs::render('cartPharmacy') }}

    <div class="row">
        <div class="col-7"><span class="border-bottom border-primary">Выбор аптеки</span></div>
        <div class="col-2 text-center">Наличие</div>

        <div class="accordion">
            @foreach($stores as $store)
                <div class="store-item">
                    <div class="store-item_title collapsed" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $store['store']->id }}">
                        <h6 class="col-7">{{ $store['store']->name }}</h6>
                        <p class="col-2 text-center text-primary">{{ count($store['products']) }} из {{ $total }} товаров</p>
                        <p class="col-1 text-end">
                            @php
                                $price = 0;
                                foreach ($store['products'] as $product)
                                    $price += $product['quantity'] * $product['price'];
                            @endphp
                            {{ $price }} &#8381;
                        </p>
                        <p class="col-2 text-end">
                            <a href="{{ route('checkout', ['store'=> $store['store']->id]) }}" class="btn btn-primary">Выбрать аптеку</a>
                        </p>
                    </div>
                    <div id="collapse-{{ $store['store']->id }}" class="collapse" data-bs-parent=".accordion">
                        <div class="description-item_body row align-items-center">
                            @foreach($store['products'] as $product)
                                @if ($product['product']->checkedPhotos->count())
                                    <img class="col-1" src="{{ url($product['product']->checkedPhotos()->first()->getUrl()) }}" alt="{{ $product['product']->name }}" />
                                @else
                                    <img class="col-1" src="{{ url(\App\Models\Photo::DEFAULT_FILE) }}" alt="{{ $product['product']->name }}" />
                                @endif
                                <div class="col-6">{{ $product['product']->name }}</div>
                                <div class="col-2 text-center">{{ $product['price'] }} &#8381; x {{ $product['quantity'] }}</div>
                                <div class="col-3"></div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
