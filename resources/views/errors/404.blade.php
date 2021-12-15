@extends('layouts.error')

@section('content')
    <h1>Неправильно набран адрес или такой страницы не существует</h1>

    <div class="image"><img style="width: 100%" src="/images/404.png" alt=""></div>

    <p class="my-3">
        <a class="btn btn-danger btn-lg" href="{{ route('home') }}">Перейти на главную</a>
    </p>
@endsection
