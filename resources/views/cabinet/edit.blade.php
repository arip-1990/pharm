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
                    <form name="edit" action="{{ route('profile.update') }}" method="POST">
                        @method('PUT')
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">ФИО</label>
                            <input name="name" class="form-control" id="name" aria-describedby="nameError" value="{{ $user->name }}">
                            @error('name')
                                <div id="nameError" class="form-text">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Почта</label>
                            <input type="email" name="email" class="form-control" id="email" aria-describedby="emailError" value="{{ $user->email }}">
                            @error('email')
                            <div id="emailError" class="form-text">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Телефон</label>
                            <input name="phone" class="form-control" id="phone" aria-describedby="phoneError" value="{{ $user->phone }}">
                            @error('phone')
                            <div id="phoneError" class="form-text">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                        <a class="btn btn-danger" href="{{ route('profile') }}">Отмена</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
