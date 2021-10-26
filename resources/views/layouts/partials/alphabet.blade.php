<?php
/** @var Illuminate\Support\Collection $alphabet */
?>

<div class="card alphabet">
    <div class="card-header">Список лекарств по алфавиту</div>
    <div class="card-body text-secondary">
        <p class="card-text m-0">
            @if (preg_grep('/\d/', $alphabet->toArray()))
                @if ('0-9' === $abc)
                    <a class="active">0-9</a>
                @else
                    <a class="pe-4" href="{{ route('alphabet', ['abc' => '0-9']) }}">0-9</a>
                @endif
            @else
                <a class="disable">0-9</a>
            @endif

            @foreach (range('A','Z') as $item)
                @if ($alphabet->contains($item))
                        @if ($item === $abc)
                            <a class="active">{{ $item }}</a>
                        @else
                            <a href="{{ route('alphabet', ['abc' => $item]) }}">{{ $item }}</a>
                        @endif
                @else
                    <a class="disable">{{ $item }}</a>
                @endif
            @endforeach
        </p>
        <p class="card-text">
            @foreach (range(chr(0xC0), chr(0xDF)) as $b)
                @if ($alphabet->contains($item = iconv('CP1251', 'UTF-8', $b)))
                    @if ($item === $abc)
                        <a class="active">{{ $item }}</a>
                    @else
                        <a href="{{ route('alphabet', ['abc' => $item]) }}">{{ $item }}</a>
                    @endif
                @else
                    <a class="disable">{{ $item }}</a>
                @endif
            @endforeach
        </p>
    </div>
</div>
