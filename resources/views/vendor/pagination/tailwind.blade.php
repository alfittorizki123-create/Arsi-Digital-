@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col sm:flex-row items-center justify-between gap-3 py-2 px-1">
        {{-- Total Items Summary --}}
        <div class="text-xs text-on-surface-variant font-medium">
            Showing <span class="font-bold text-on-surface">{{ $paginator->firstItem() ?? 0 }}</span> to <span class="font-bold text-on-surface">{{ $paginator->lastItem() ?? 0 }}</span> of <span class="font-bold text-primary">{{ number_format($paginator->total()) }}</span> results
        </div>

        {{-- Page Numbers & Next/Previous Buttons --}}
        <div class="flex items-center gap-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                    <span class="inline-flex items-center justify-center h-8 px-3 rounded-lg border border-outline-variant bg-surface-container/30 text-on-surface-variant/40 cursor-not-allowed text-xs font-bold shadow-xs">
                        &laquo; Previous
                    </span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center h-8 px-3 rounded-lg border border-outline-variant bg-surface text-on-surface hover:bg-primary hover:text-on-primary font-bold text-xs shadow-xs transition-colors" aria-label="{{ __('pagination.previous') }}">
                    &laquo; Previous
                </a>
            @endif

            {{-- Page Number Links --}}
            @foreach ($elements as $element)
                {{-- Three Dots Separator --}}
                @if (is_string($element))
                    <span aria-disabled="true">
                        <span class="inline-flex items-center justify-center min-w-[32px] h-8 px-2 rounded-lg border border-outline-variant bg-surface text-on-surface-variant text-xs font-bold cursor-default">{{ $element }}</span>
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page">
                                <span class="inline-flex items-center justify-center min-w-[32px] h-8 px-2 rounded-lg bg-primary text-on-primary font-bold text-xs shadow-sm">{{ $page }}</span>
                            </span>
                        @else
                            <a href="{{ $url }}" class="inline-flex items-center justify-center min-w-[32px] h-8 px-2 rounded-lg border border-outline-variant bg-surface text-on-surface hover:bg-surface-container font-bold text-xs shadow-xs transition-colors" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center h-8 px-3 rounded-lg border border-outline-variant bg-surface text-on-surface hover:bg-primary hover:text-on-primary font-bold text-xs shadow-xs transition-colors" aria-label="{{ __('pagination.next') }}">
                    Next &raquo;
                </a>
            @else
                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                    <span class="inline-flex items-center justify-center h-8 px-3 rounded-lg border border-outline-variant bg-surface-container/30 text-on-surface-variant/40 cursor-not-allowed text-xs font-bold shadow-xs">
                        Next &raquo;
                    </span>
                </span>
            @endif
        </div>
    </nav>
@endif
