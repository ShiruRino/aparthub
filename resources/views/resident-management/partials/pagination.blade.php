@php
    /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator */
    $window = $elements ?? $paginator->linkCollection();
@endphp

<div class="resident-pagination">
    @if ($paginator->onFirstPage())
        <span class="resident-page-btn">&lt;</span>
    @else
        <a class="resident-page-btn" href="{{ $paginator->previousPageUrl() }}">&lt;</a>
    @endif

    @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 1), min($paginator->lastPage(), $paginator->currentPage() + 1)) as $page => $url)
        @if ($page === $paginator->currentPage())
            <span class="resident-page-btn active">{{ $page }}</span>
        @else
            <a class="resident-page-btn" href="{{ $url }}">{{ $page }}</a>
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
        <a class="resident-page-btn" href="{{ $paginator->nextPageUrl() }}">&gt;</a>
    @else
        <span class="resident-page-btn">&gt;</span>
    @endif

    <span>
        Showing {{ $paginator->firstItem() ?? 0 }}-{{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }}
    </span>
</div>
