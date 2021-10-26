<nav class="nav left-nav">
    @switch(\Illuminate\Support\Facades\Route::currentRouteName())
        @case('profile')
            <span class="personal-links active">Профиль</span>
            <a class="personal-links" href="{{ url('/profile/order') }}">Заказы</a>
            <a class="personal-links" href="{{ route('favorite') }}">Избранное</a>
            @break
        @case('order')
            <a class="personal-links" href="{{ route('profile') }}">Профиль</a>
            <span class="personal-links active">Заказы</span>
            <a class="personal-links" href="{{ route('favorite') }}">Избранное</a>
            @break
        @case('favorite')
            @auth()
                <a class="personal-links" href="{{ route('profile') }}">Профиль</a>
                <a class="personal-links" href="{{ url('/profile/order') }}">Заказы</a>
            @else
                <a class="personal-links" href="{{ route('profile') }}" data-toggle="modal" data-target="login">Профиль</a>
                <a class="personal-links" href="{{ url('/profile/order') }}" data-toggle="modal" data-target="login">Заказы</a>
            @endauth
            <span class="personal-links active">Избранное</span>
            @break
    @endswitch
</nav>
