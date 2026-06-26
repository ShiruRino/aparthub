@php
    /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator */
@endphp

<div class="resident-pagination">
    @if ($paginator->onFirstPage())
        <span class="resident-page-btn" aria-disabled="true">&lt;</span>
    @else
        <a class="resident-page-btn" href="{{ $paginator->previousPageUrl() }}" aria-label="Halaman sebelumnya">&lt;</a>
    @endif

    @php
        $startPage = max(1, $paginator->currentPage() - 1);
        $endPage = min($paginator->lastPage(), $paginator->currentPage() + 1);
    @endphp

    @if ($startPage > 1)
        <a class="resident-page-btn" href="{{ $paginator->url(1) }}">1</a>
        @if ($startPage > 2)
            <span class="resident-page-gap">...</span>
        @endif
    @endif

    @foreach ($paginator->getUrlRange($startPage, $endPage) as $page => $url)
        @if ($page === $paginator->currentPage())
            <span class="resident-page-btn active" aria-current="page">{{ $page }}</span>
        @else
            <a class="resident-page-btn" href="{{ $url }}">{{ $page }}</a>
        @endif
    @endforeach

    @if ($endPage < $paginator->lastPage())
        @if ($endPage < $paginator->lastPage() - 1)
            <span class="resident-page-gap">...</span>
        @endif
        <a class="resident-page-btn" href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a>
    @endif

    @if ($paginator->hasMorePages())
        <a class="resident-page-btn" href="{{ $paginator->nextPageUrl() }}" aria-label="Halaman berikutnya">&gt;</a>
    @else
        <span class="resident-page-btn" aria-disabled="true">&gt;</span>
    @endif

    <span class="resident-pagination-meta">
        Showing {{ $paginator->firstItem() ?? 0 }}-{{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }}
    </span>
</div>
