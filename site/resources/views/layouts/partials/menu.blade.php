<li>
    <a href="{{ route('catalog', ['category' => $category->slug]) }}">
        @if ($category->isRoot())
            <img src="/images/category/cat_{{ $category->id }}.png" alt="">
        @endif
        {{ $category->name }}
    </a>
    @if ($category->descendants()->count())
        <div class="overlay">
            <ul>
                @foreach ($category->descendants as $category)
                    @include('layouts.partials.menu', $category)
                @endforeach
            </ul>
        </div>
    @endif
</li>
