<li>
    <a href="{{ route('catalog', ['category' => $category->slug]) }}">
        @if (!$category->parent)
            <img src="/images/category/cat_{{ $category->id }}.png" alt="">
        @endif
        {{ $category->name }}
    </a>
    @if ($category->children()->count())
        <div class="overlay">
            <ul>
                @foreach ($category->children as $category)
                    @include('layouts.partials.menu', $category)
                @endforeach
            </ul>
        </div>
    @endif
</li>
