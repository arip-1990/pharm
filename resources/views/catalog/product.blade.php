@extends('layouts.default')

@section('banner', '')

@section('content')
    <div class="row justify-content-center mb-3" data-product="{{ $product->id }}">
        <div class="col-8 col-sm-7 col-md-5 col-lg-3 position-relative">
            @if ($product->photos->count())
                <img class="w-100" src="{{ url($product->photos->first()->getOriginalFile()) }}" alt="{{ $product->name }}" />
            @else
                <img class="w-100" src="{{ url(\App\Entities\Photo::DEFAULT_FILE) }}" alt="{{ $product->name }}" />
            @endif

            @if (in_array($product->id, session('favorites', [])))
                <img alt="" src="/images/heart.png" style="left: 1.5rem" class="favorite-toggle" data-action="remove">
            @else
                <img alt="" src="/images/fav.png" style="left: 1.5rem" class="favorite-toggle" data-action="add">
            @endif
        </div>

        <div class="col-12 col-lg-9 d-flex flex-column justify-content-around">
            <h4 class="text-center">{{ $product->name }}</h4>

            <div class="row" style="min-height: 50%">
                <div class="col-12 col-lg-7 col-xxl-8 mb-3 mb-lg-0">
                    @if (count($product->values))
                        <div style="background: #e6eded;padding: .75rem;">
                            @foreach ($product->values as $value)
                                @switch ($value->attribute->name)
                                    @case('Производитель')
                                    @case('Страна')
                                    @case('Действующее вещество')
                                    @case('Условия отпуска из аптек')
                                    <h6>
                                        <b class="me-2">{{ $value->attribute->name }}:</b>
                                        {{ html_entity_decode($value->value) }}
                                    </h6>
                                @endswitch
                            @endforeach
                            <h6>
                                <b class="collapsed description-info" data-bs-toggle="collapse" data-bs-target="#product-desc">Информация о товаре</b>
                            </h6>
                        </div>
                    @endif
                </div>
                <div class="col-12 col-lg-5 col-xxl-4 d-flex flex-column justify-content-between">
                    @if ($minPrice)
                        <h4 class="text-end">Цена: от <span>{{ $minPrice }}</span> р.</h4>

                        <div class="row align-items-center">
                            <div class="col-6">
                                <div class="input-group input-product">
                                    <button class="btn btn-outline-primary" data-type="minus">-</button>
                                    <input type="number" class="form-control input-number" min="1" max="{{ $product->getCount() }}" value="1" />
                                    <button class="btn btn-outline-primary" data-type="plus">+</button>
                                </div>
                            </div>

                            <div class="col-6 text-end">
                                <a class="btn btn-primary btn-lg cart-add" data-action="add">В корзину</a>
                            </div>
                        </div>
                    @else
                        <h4 class="text-center">Нет в наличии</h4>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div id="product-desc" class="description collapse">
        @if (trim($product->description))
            <div class="description-item">
                <h6 class="description-item_title collapsed" data-bs-toggle="collapse" data-bs-target="#collapse-1">Описание</h6>
                <div id="collapse-1" class="collapse" data-bs-parent="#product-desc">
                    <div class="description-item_body">{!! str_replace(PHP_EOL, '<br />', html_entity_decode($product->description)) !!}</div>
                </div>
            </div>
        @endif

        @foreach ($product->values as $i => $value)
            @if ($value->value)
                <div class="description-item">
                    <h6 class="description-item_title collapsed" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $i + 2 }}">{{ $value->attribute->name }}</h6>
                    <div id="collapse-{{ $i + 2 }}" class="collapse" data-bs-parent="#product-desc">
                        <div class="description-item_body">{!! $value->attribute->type === \App\Entities\Attribute::TYPE_TEXT ? html_entity_decode(implode('<br />', json_decode($value->value, true))) : html_entity_decode($value->value) !!}</div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    @if ($minPrice)
        <div class="row p-2 fw-bold d-md-flex m-0" style="display: none; background: #f4f4f4; color: #757a7a;">
            <div class="col-md-5 text-center">Адрес</div>
            <div class="col-md-3 text-center">Время работы</div>
            <div class="col-md-2 text-center">Цена</div>
            <div class="col-md-2 text-center">Количество</div>
        </div>

        @foreach ($offers as $offer)
            @if ($offer->store)
                <div class="row align-items-center border-top p-2 m-0">
                    <div class="col-12 col-md-5">
                        <b>{{ $offer->store->name }}</b>
                    </div>
                    <div class="col-12 col-md-3 text-md-center">
                        <b class="d-md-none">Время работы: </b>{!! \App\Helper::formatSchedule($offer->store->schedule) !!}
                    </div>
                    <div class="col-12 col-md-2 text-md-center">
                        <b class="d-md-none">Цена: </b>{{ $offer->price }} р.
                    </div>
                    <div class="col-12 col-md-2 text-md-center">
                        <b class="d-md-none">Количество:</b>
                        @if($offer->quantity >= 10)
                            много
                        @else
                            {{ $offer->quantity }} шт.
                        @endif
                    </div>
                </div>
            @else
                {{ $offer->store_id }}
            @endif
        @endforeach
    @endif
@endsection
