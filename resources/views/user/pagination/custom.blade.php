@if ($paginator->hasPages())
    <nav class="yk-pager" role="navigation" aria-label="Phân trang">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="yk-pager__btn is-disabled" aria-disabled="true" aria-label="Trang trước">
                <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="yk-pager__btn" rel="prev" aria-label="Trang trước">
                <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
            </a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="yk-pager__gap" aria-hidden="true">…</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="yk-pager__btn is-current" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="yk-pager__btn">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="yk-pager__btn" rel="next" aria-label="Trang sau">
                <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
            </a>
        @else
            <span class="yk-pager__btn is-disabled" aria-disabled="true" aria-label="Trang sau">
                <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
            </span>
        @endif
    </nav>
@endif
