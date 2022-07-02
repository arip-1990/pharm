@extends('layouts.default')

@section('banner', '')

@section('content')
    {{ \Diglactic\Breadcrumbs\Breadcrumbs::render('checkout', $store) }}

    <h1 class="text-center">Оформление заказа</h1>
    <form method="post" class="row row-cols-1 checkout">
        @csrf
        <input type="hidden" name="store" value="{{ $store->id }}">
        <div class="col-md-8 p-4" style="border: 2px solid #f7f7f7;">
            <h4 class="text-center">Способ получения</h4>
            <div class="accordion" id="deliveryAddress">
                <div class="row accordion-header" id="headingPickup">
                    <div class="col-10 col-lg-6 col-xl-5 col-xxl-4 offset-1 offset-lg-0 offset-xl-1">
                        <label class="radio-button{{ old('delivery', 0) == 0 ? ' active' : '' }}" data-bs-toggle="collapse" data-bs-target="#collapseEmpty" aria-controls="collapseEmpty">
                            <input type="radio" name="delivery" class="radio-button_pin" value="0" checked />
                            <p class="radio-button_text">Самовывоз<span>Бесплатно</span></p>
                        </label>
                    </div>
                    <div class="col-10 col-lg-6 col-xl-5 col-xxl-5 offset-1 offset-lg-0 offset-xl-1">
                        Вы можете совершить покупку и забрать свой заказ самостоятельно, приехав в аптеку.
                    </div>
                </div>
                <div id="collapseEmpty" class="accordion-collapse collapse" aria-labelledby="headingPickup" data-bs-parent="#deliveryAddress"></div>

                <div class="row accordion-header" id="headingDelivery">
                    <div class="col-10 col-lg-6 col-xl-5 col-xxl-4 offset-1 offset-lg-0 offset-xl-1">
                        <label class="radio-button{{ old('delivery', 0) == 1 ? ' active' : '' }}" data-bs-toggle="collapse" data-bs-target="#collapse" aria-controls="collapse">
                            @if(session('recipe', false))
                                <input type="radio" class="radio-button_pin" disabled />
                            @else
                                <input type="radio" name="delivery" class="radio-button_pin" value="1" />
                            @endif
                            <p class="radio-button_text">Доставка<span>Указать адрес доставки.</span></p>
                        </label>
                    </div>
                    <div class="col-10 col-lg-6 col-xl-5 col-xxl-5 offset-1 offset-lg-0 offset-xl-1">
                        Доставка осуществляется с 9:00 до 21:00, без выходных. Доставка осуществляется по тарифам такси.
                    </div>
                </div>
                <div id="{{session('recipe', false) ? '' : 'collapse'}}" class="accordion-collapse collapse{{ (!session('recipe', false) and old('delivery', 0) == 1) ? ' show' : '' }}" aria-labelledby="headingDelivery" data-bs-parent="#deliveryAddress">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-sm-3 offset-xl-1">
                                <label for="city" class="form-label">Город</label>
                                <select class="form-select" id="city" name="city" aria-label="Город">
                                    @php $city = Illuminate\Support\Facades\Cookie::get('city', config('data.city')[0]) @endphp
                                    @foreach (config('data.city') as $item)
                                        @if($city == $item)
                                            <option value="{{$item}}" selected>{{$item}}</option>
                                        @else
                                            <option value="{{$item}}">{{$item}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-5">
                                <label for="street" class="form-label">Улица</label>
                                <input name="street" class="form-control @error('street') is-invalid @enderror" id="street" placeholder="Улица" value="{{ old('street') }}">
                                @error('street') <p style="font-size: 0.75rem; font-weight: 300" class="text-danger">Поле обязательно для заполнения.</p> @enderror
                            </div>
                            <div class="col-sm-2">
                                <label for="house" class="form-label">Дом</label>
                                <input name="house" class="form-control @error('house') is-invalid @enderror" id="house" placeholder="Дом" value="{{ old('house') }}">
                                @error('house') <p style="font-size: 0.75rem; font-weight: 300" class="text-danger">Поле обязательно для заполнения.</p> @enderror
                            </div>
                        </div>
                        <div class="row my-3">
                            <div class="col-sm-3 offset-xl-1">
                                <label for="entrance" class="form-label">Подъезд</label>
                                <input name="entrance" class="form-control" id="entrance" placeholder="Подъезд" value="{{ old('entrance') }}">
                            </div>
                            <div class="col-sm-4">
                                <label for="floor" class="form-label">Этаж</label>
                                <input name="floor" class="form-control" id="floor" placeholder="Этаж" value="{{ old('floor') }}">
                            </div>
                            <div class="col-sm-3">
                                <label for="apartment" class="form-label">Квартира</label>
                                <input name="apartment" class="form-control" id="apartment" placeholder="Квартира" value="{{ old('apartment') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-10 offset-1 offset-lg-0 offset-xl-1">
                                <input class="form-check-input" type="checkbox" name="service_to_door" value="{{ old('service_to_door') }}" id="service_to_door">
                                <label class="form-check-label" for="service_to_door">Доставка до двери</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h4 class="text-center p-4 mt-3" style="border-top: 2px solid #f7f7f7;">Способ оплаты</h4>
            <div class="row">
                <div class="col-10 col-lg-5 col-xl-4 offset-1 offset-lg-0 offset-xl-1">
                    <label class="radio-button active">
                        <input type="radio" name="payment" class="radio-button_pin" value="0" checked />
                        <p class="radio-button_text">Оплата наличными<span>При получении</span></p>
                    </label>
                </div>
                <div class="col-10 col-lg-5 col-xl-4 offset-1 offset-lg-0 offset-lg-2">
                    <label class="radio-button">
                        <input type="radio" name="payment" class="radio-button_pin" value="1" />
                        <p class="radio-button_text">Оплата картой<span><img style="height: 20px" src="/images/payments.png" alt=""></span></p>
                    </label>
                </div>
            </div>

            <div class="row my-3">
                <div class="col offset-1 offset-lg-0 offset-xl-1">
                    <div class="form-check">
                        <input class="form-check-input @error('rule') is-invalid @enderror" type="checkbox" name="rule" id="rule">
                        <label class="form-check-label" for="rule">Я согласен(а) с правилами сайта</label>
                        @error('rule') <p style="font-size: 0.75rem; font-weight: 300" class="text-danger">Обязательно для заполнения.</p> @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 p-4" style="border: 2px solid #f7f7f7;">
            <h4 class="text-center">Ваш заказ</h4>
            @foreach($cartService->getItems() as $item)
                <div class="row">
                    <div class="col-6 col-md-8 offset-1 offset-md-0">
                        <a href="{{ route('catalog.product', ['product' => $item->product->slug]) }}">{{ $item->product->name }}</a>
                    </div>
                    <div class="col-4 col-md-4 text-end">
                        {{ $item->product->getPrice($store) }}&#8381;<br />
                        <span class="text-muted">x {{ $item->quantity }}</span>
                    </div>
                </div>
            @endforeach

            <div class="row py-3 mt-3" style="border-top: 2px solid #f7f7f7;">
                <div class="col-5 col-md-6 col-lg-8 offset-1 offset-md-0">Стоимость:</div>
                <div class="col-5 col-md-6 col-lg-4 text-end">{{ $cartService->getAmount() }}&#8381;</div>
            </div>
            <div class="row">
                <div class="col-5 col-md-6 col-lg-8 offset-1 offset-md-0">Самовывоз:</div>
                <div class="col-5 col-md-6 col-lg-4 text-end">Бесплатно</div>
            </div>

            <div class="row py-3 mt-3" style="border-top: 2px solid #f7f7f7;">
                <h5 class="col-5 col-md-6 col-lg-8 offset-1 offset-md-0">Итого:</h5>
                <h5 class="col-5 col-md-6 col-lg-4 text-end">{{ $cartService->getAmount() }}&#8381;</h5>
            </div>

            <div class="text-center">
                <button class="btn btn-primary" type="submit">Подтвердить заказ</button>
            </div>
        </div>
    </form>
@endsection
