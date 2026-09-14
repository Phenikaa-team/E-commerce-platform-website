@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex gap-2 items-center justify-between">
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-200 cursor-not-allowed leading-5 rounded-xl shadow-xs">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 leading-5 rounded-xl hover:text-[#ea384c] hover:border-[#ea384c] focus:outline-none transition ease-in-out duration-150 shadow-xs">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 leading-5 rounded-xl hover:text-[#ea384c] hover:border-[#ea384c] focus:outline-none transition ease-in-out duration-150 shadow-xs">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-200 cursor-not-allowed leading-5 rounded-xl shadow-xs">
                {!! __('pagination.next') !!}
            </span>
        @endif
    </nav>
@endif
