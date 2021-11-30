@extends('layouts.default')

@section('banner', '')

@section('content')
    <div class="row">
        <h3>Состав заказа</h3>

        <div class="cart">
            <div class="row cart_header d-md-flex">
                <div class="col-2 text-center"></div>
                <div class="col-5 text-center">Название</div>
                <div class="col-2 text-center">Цена</div>
                <div class="col-2 text-center">Количество</div>
                <div class="col-1 text-center"></div>
            </div>
            @php $total = 0; @endphp
            @foreach ($cartItems as $item)
                @php $total += $item->getAmount(); @endphp
                <div class="row align-items-center product" data-product="{{ $item->product->id }}">
                    <div class="col-3 col-md-2 text-center">
                        @if ($item->product->photos()->count())
                            <img alt="" src="{{ $item->product->photos[0]->getOriginalFile() }}">
                        @else
                            <img alt="" src="{{ App\Entities\Photo::DEFAULT_FILE }}">
                        @endif
                    </div>
                    <div class="col-7 col-md-5 product_title">
                        <p><a href="{{ route('catalog.product', ['product' => $item->product->slug]) }}">{{ $item->product->name }}</a></p>
                        <p>{{ $item->product->getValue(5) /*?? $product->getValue(38)*/ }}</p>
                    </div>
                    <div class="col-6 col-md-2 order-4 order-md-0 text-md-center product_price">
                        <span>{!! 'от ' . $item->product->getPrice() . ' &#8381;' !!}</span>
                    </div>
                    <div class="col-6 col-md-2 order-5 order-md-0">
                        <div class="input-group input-product">
                            <button class="btn btn-outline-primary" data-type="-">-</button>
                            <input type="number" class="form-control input-number" min="1" max="{{ $item->product->getCount() }}" value="{{ $item->quantity }}" />
                            <button class="btn btn-outline-primary" data-type="+">+</button>
                        </div>
                    </div>
                    <span class="col-2 col-md-1 cart-remove" data-action="remove"></span>
                </div>
            @endforeach

            <div class="row align-items-center mt-3">
                <p class="col-12 col-md-8">
                    В процессе оформления заказа цена может незначительно измениться в зависимости от выбранной аптеки.<br />
                    Цены на сайте отличаются от цен в аптеках и действуют только при оформлении заказа с помощью сайта.
                </p>
                <p class="col-12 col-md-4 text-center text-md-end fs-4 fw-bold">Итого: от <span id="total-price">{{ $total }}</span> &#8381;</p>
            </div>

            <div class="row align-items-center mt-3">
                <div class="col-12 col-md-6 order-md-1 text-center text-md-end">
                    @auth()
                        <a href="{{ route('cart.pharmacy') }}" class="btn btn-primary">
                            Оформить заказ
                        </a>
                    @else
                        <a href="{{ route('cart.pharmacy') }}" class="btn btn-primary" data-toggle="modal" data-target="login">
                            Оформить заказ
                        </a>
                    @endauth
                </div>

                <div class="col-12 col-md-6 text-center text-md-start mt-3 mt-md-0">
                    <a href="{{ url('/catalog') }}" class="btn btn-outline-primary">Вернуться к покупкам</a>
                </div>
            </div>
        </div>
    </div>
@endsection
