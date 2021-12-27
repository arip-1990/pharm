@extends('layouts.default')

@section('banner', '')

@section('content')
    {{ \Diglactic\Breadcrumbs\Breadcrumbs::render('favorite') }}

    <div class="row">
        <aside class="col-3 sidebar">
            @include('layouts.partials.sidebar')
        </aside>

        <div class="col-9">
            <div class="card">
                <div class="card-header">Избранное</div>
                <div class="card-body">
                    <?php /** @var App\Entities\Product $product */ ?>
                    @foreach ($paginator as $product)
                        <div class="row favorite" data-product="{{ $product->id }}">
                            <div class="col-3 col-md-3">
                                @if ($product->photos()->count())
                                    <img alt="" src="{{ $product->photos[0]->getOriginalFile() }}" style="height: 120px;width: 120px;">
                                @else
                                    <img alt="" src="{{ App\Entities\Photo::DEFAULT_FILE }}" style="height: 120px;width: 120px;">
                                @endif
                            </div>
                            <div class="col-7 col-md-8">
                                <a class="favorite_title" href="{{ route('catalog.product', ['product' => $product->slug]) }}">{{ $product->name }}</a>
                            </div>
                            <span class="col-2 col-md-1 favorite-remove"></span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{ $paginator->links('layouts.partials.pagination') }}
        </div>
    </div>
@endsection
