@extends('layouts.default')

@section('banner', '')

@section('content')
    @include('layouts.partials.alphabet')

    <h3>{{ $abc }}</h3>
    <div class="row">
        @foreach ($paginator as $product)
            <p class="col-6">
                <a class="product-link" href="{{ route('catalog.product', ['product' => $product->slug]) }}">{{ $product->name }}</a>
            </p>
        @endforeach
    </div>

    {{ $paginator->links('layouts.partials.pagination') }}
@endsection
