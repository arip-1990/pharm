@extends('layouts.error')

@section('content')
    <h1></h1>

    <p>{{ $exception->getMessage() }}</p>

    <p class="my-3">
        <a class="btn btn-danger btn-lg" href="{{ route('home') }}">Перейти на главную</a>
    </p>
@endsection
