@extends('layouts.default')

@section('banner', '')

@section('content')
    <div class="row">
        <nav class="col-md-3">
            <ul class="category">
                @each ('layouts.partials.menu', $categories, 'category')
            </ul>
        </nav>

        <div class="col-md-9">
            @if ($pagination->count())
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3 g-lg-4" itemscope itemtype="https://schema.org/ItemList">
                    <link itemprop="url" href="{{ url()->current() }}">
                    @foreach ($pagination as $product)
                        <div class="col-10 offset-1 offset-sm-0">
                            <div class="card product" itemprop="itemListElement" itemscope itemtype="https://schema.org/Product" data-product="{{ $product->id }}">
                                @if ('По рецепту' === $value = $product->getValue(4))
                                    <div class="card-mod card-mod__prescription">
                                        <div class="card-mod_icon"></div>
                                        <div class="card-mod_text">По рецепту</div>
                                    </div>
                                @elseif ('По назначению врача' === $value)
                                    <div class="card-mod card-mod__appointment">
                                        <div class="card-mod_icon"></div>
                                        <div class="card-mod_text">По назначению врача</div>
                                    </div>
                                @else
                                    <div class="card-mod card-mod__delivery">
                                        <div class="card-mod_icon"></div>
                                        <div class="card-mod_text">Доставка</div>
                                    </div>
                                @endif
                                @if ($product->photos->count())
                                    <img class="card-img-top mt-2" itemprop="image" src="{{ url($product->photos->first()->getOriginalFile()) }}" alt="{{ $product->name }}" />
                                @else
                                    <img class="card-img-top mt-2" itemprop="image" src="{{ url(\App\Entities\Photo::DEFAULT_FILE) }}" alt="{{ $product->name }}" />
                                @endif
                                @if (in_array($product->id, session('favorites', [])))
                                    <img alt="" src="/images/heart.png" class="favorite-toggle" data-action="remove">
                                @else
                                    <img alt="" src="/images/fav.png" class="favorite-toggle" data-action="add">
                                @endif
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <a class="product-link" itemprop="url" href="{{ route('catalog.product', ['product' => $product->slug]) }}">
                                            <span itemprop="name">{{ $product->name }}</span>
                                        </a>
                                    </h6>
                                    <div class="card-text">
                                        @if ($count = $product->getCountByCity($city))
                                            <p class="marker"><i class="fas fa-map-marker-alt"></i> {{ "В наличии в $count " . ($count === 1 ? 'аптеке' : 'аптеках') }}</p>
                                            <div class="price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                                                <p class="mask">Показать цену</p>
                                                <p class="real" itemprop="price"></p>
                                            </div>
                                        @else
                                            <p class="marker marker__red"><i class="fas fa-map-marker-alt"></i> Нет в наличии</p>
                                        @endif
                                    </div>
                                    @if($cartService->getItems()->contains(fn(\App\Entities\CartItem $item) => $item->product_id === $product->id))
                                        <a class="btn btn-primary btn-lg">Добавлено</a>
                                    @else
                                        <a class="btn btn-primary btn-lg" data-toggle="modal" data-target="product" data-max="{{ $product->getCount() }}">
                                            Добавить в корзину <i class="fas fa-caret-right" style="vertical-align: middle"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{ $pagination->onEachSide(2)->links('layouts.partials.pagination') }}
            @else
                <h3 class="text-center">Товары отсутствуют</h3>
            @endif
        </div>
    </div>
@endsection
