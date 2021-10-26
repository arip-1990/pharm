@extends('layouts.error')

@section('content')
    <h1>{{ $exception->getMessage() }}</h1>

    <div class="image"><img style="width: 100%" src="/images/404.png" alt=""></div>
    <p>Неправильно набран адрес или такой страницы не существует</p>
    <p class="my-3">
        <a class="btn btn-danger btn-lg" href="{{ route('home') }}">Перейти на главную</a>
    </p>
@endsection
