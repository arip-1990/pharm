@extends('layouts.default')

@section('banner', '')

@section('content')
    {{ \Diglactic\Breadcrumbs\Breadcrumbs::render('loyalty') }}

    <h3 class="text-center">Программа лояльности</h3>

    <div class="page"></div>
@endsection
