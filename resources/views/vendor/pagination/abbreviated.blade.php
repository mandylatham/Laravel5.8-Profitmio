@if ($paginator->hasPages())
    <ul class="pagination pagination-sm">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="disabled page-item"><span class="page-link">@lang('pagination.first')</span></li>
            <li class="disabled page-item"><span class="page-link">@lang('pagination.previous')</span></li>
        @else
            <li class="page-item"><a href="{{ $paginator->url(1) }}" class="page-link">@lang('pagination.first')</a></li>
            <li class="page-item"><a href="{{ $paginator->previousPageUrl() }}" class="page-link" rel="prev">@lang('pagination.previous')</a></li>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="page-item"><a href="{{ $paginator->nextPageUrl() }}" class="page-link" rel="next">@lang('pagination.next')</a></li>
            <li class="page-item"><a href="{{ $paginator->url($paginator->lastPage()) }}" class="page-link" rel="next">@lang('pagination.last')</a></li>
        @else
            <li class="disabled page-item"><span class="page-link">@lang('pagination.last')</span></li>
            <li class="disabled page-item"><span class="page-link">@lang('pagination.next')</span></li>
        @endif
    </ul>
@endif
