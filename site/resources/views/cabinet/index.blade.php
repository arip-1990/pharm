@extends('layouts.default')

@section('banner', '')

@section('content')
    <div class="row">
        <aside class="col-3 sidebar">
            @include('layouts.partials.sidebar')
        </aside>

        <div class="col-9">
            <div class="card">
                <div class="card-header">Профиль</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tbody>
                        <tr>
                            <th scope="row">ФИО</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Почта</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Телефон</th>
                            <td>{{ \App\Helper::formatPhone($user->phone, true) }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Дата регистрации</th>
                            <td>{{ $user->created_at->isoFormat('DD MMMM YYYY, HH:mm:ss') }}</td>
                        </tr>
                        </tbody>
                    </table>

                    <a class="btn btn-primary" href="{{ route('profile.edit') }}">Изменить данные</a>
                </div>
            </div>
        </div>
    </div>
@endsection
