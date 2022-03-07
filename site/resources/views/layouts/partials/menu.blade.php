<li>
    <a href="{{ route('catalog', ['category' => $category->slug]) }}">
        @if($category->isRoot())
            <img src="/images/category/cat_{{ $category->id }}.png" alt="">
        @endif
        {{ $category->name }}
    </a>
    @if($category->children->count())
        <div class="overlay">
            <ul>
                @foreach($category->children as $category)
                    @if($loop->index >= 10)
                        @break
                    @endif

                    @include('layouts.partials.menu', $category)
                @endforeach

                @if($category->parent->children->count() > 10)
                    <li>
                        <a href="{{ route('catalog', ['category' => $category->parent->slug]) }}">Еще</a>
                    </li>
                @endif
            </ul>
        </div>
    @endif
</li>
