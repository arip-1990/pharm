@extends('layouts.default')

@section('banner', '')

@section('content')
    {{ \Diglactic\Breadcrumbs\Breadcrumbs::render('catalogProduct', $product) }}

    <div class="row align-items-center mb-3" itemscope itemtype="https://schema.org/Product" data-product="{{ $product->id }}">
        <div class="col-8 col-sm-6 col-lg-3 offset-2 offset-sm-3 offset-lg-0 position-relative text-center">
            @forelse($product->checkedPhotos as $photo)
                @if($loop->first)
                    @if($photo->getSize()[0] < 420)
                        <img class="mw-100 m-auto" style="cursor: zoom-in" itemprop="image" data-toggle="carousel" data-target=".carousel" src="{{ $photo->getUrl() }}" alt="{{ $product->name }}" />
                    @else
                        <figure class="zoom" style="background-image: url({{ $photo->getUrl() }})">
                            <img class="mw-100 m-auto" itemprop="image" data-toggle="carousel" data-target=".carousel" src="{{ $photo->getUrl() }}" alt="{{ $product->name }}" />
                        </figure>
                    @endif
                @else
                    <img class="mw-100 m-auto" style="display: none" itemprop="image" data-toggle="carousel" data-target=".carousel" src="{{ $photo->getUrl() }}" alt="{{ $product->name }}" />
                @endif
            @empty
                <img class="mw-100" itemprop="image" src="{{ url(\App\Models\Photo::DEFAULT_FILE) }}" alt="{{ $product->name }}" />
            @endforelse

            @if (in_array($product->id, session('favorites', [])))
                <img alt="" src="/images/heart.png" class="favorite-toggle" data-action="remove">
            @else
                <img alt="" src="/images/fav.png" class="favorite-toggle" data-action="add">
            @endif
        </div>

        <div class="col-12 col-lg-6 my-3">
            <div class="row">
                <h4 class="text-center mb-5" itemprop="name">{{ $product->name }}</h4>
                @if ($product->values->count())
                    <div style="background: #e6eded;padding: .75rem;">
                        @foreach ($product->values as $value)
                            @switch ($value->attribute->name)
                                @case('Производитель')
                                @case('Страна')
                                @case('Бренд')
                                @case('Действующее вещество')
                                @case('Условия отпуска из аптек')
                                    <h6>
                                        <b class="me-2">{{ $value->attribute->name }}:</b>
                                        {{ html_entity_decode($value->value) }}
                                    </h6>
                            @endswitch
                        @endforeach
                        <h6>
                            <b class="collapsed description-info text-primary" data-bs-toggle="collapse" data-bs-target="#product-desc">Информация о товаре</b>
                        </h6>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-12 col-lg-3">
            <div class="row">
                @if ($minPrice)
                    <div class="col-6 col-lg-12 text-lg-end">
                        <h5 class="price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                            <p itemprop="price">
                                Цена: от <span style="font-size: 1.75rem; font-weight: 600">{{ $minPrice }}</span> &#8381;
                            </p>
                        </h5>
                    </div>

                    <div class="col-6 col-lg-12 text-end">
                        @if($item)
                            <div class="input-group input-product">
                                <button class="btn btn-outline-primary" data-type="-">-</button>
                                <input type="number" class="form-control input-number" min="1" max="{{ $product->getQuantity() }}" value="{{ $item->quantity }}" />
                                <button class="btn btn-outline-primary" data-type="+">+</button>
                            </div>
                        @else
                            <a class="btn btn-primary" data-toggle="modal" data-target="product" data-max="{{ $product->getQuantity() }}">
                                Добавить в корзину <i class="fas fa-caret-right" style="vertical-align: middle"></i>
                            </a>
                        @endif
                    </div>
                @else
                    <h4 class="text-center">Нет в наличии</h4>
                @endif
            </div>
        </div>
    </div>

    <div id="product-desc" class="description collapse">
        @if (trim($product->description))
            <div class="description-item">
                <h6 class="description-item_title collapsed" data-bs-toggle="collapse" data-bs-target="#collapse-1">Описание</h6>
                <div id="collapse-1" class="collapse" data-bs-parent="#product-desc">
                    <div class="description-item_body" itemprop="description">{!! str_replace(PHP_EOL, '<br />', html_entity_decode($product->description)) !!}</div>
                </div>
            </div>
        @endif

        @foreach ($product->values as $i => $value)
            @if ($value->value)
                <div class="description-item">
                    <h6 class="description-item_title collapsed" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $i + 2 }}">{{ $value->attribute->name }}</h6>
                    <div id="collapse-{{ $i + 2 }}" class="collapse" data-bs-parent="#product-desc">
                        <div class="description-item_body">{!! html_entity_decode($value->value) !!}</div>
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
                        <span style="font-weight: 600">{{ $offer->store->name }}</span>
                    </div>
                    <div class="col-12 col-md-3 text-md-center">
                        <b class="d-md-none">Время работы: </b>{!! \App\Helper::formatSchedule($offer->store->schedule->toArray()) !!}
                    </div>
                    <div class="col-12 col-md-2 text-md-center">
                        <b class="d-md-none">Цена: </b>
                        <span style="font-size: 1.25rem; font-weight: 600">{{ $offer->price }}</span> &#8381;
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

    @include('layouts.partials.carousel')
@endsection
