@props(['paginator'])

@if($paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="{{ __('Pagination Navigation') }}">
        @if($paginator->onFirstPage())
            <span class="pagination__link pagination__link--disabled" aria-disabled="true">
                {{ __('Previous') }}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" 
               class="pagination__link pagination__link--prev"
               rel="prev"
               aria-label="{{ __('Go to previous page') }}">
                {{ __('Previous') }}
            </a>
        @endif
        
        <div class="pagination__pages">
            @foreach($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                @if($page == $paginator->currentPage())
                    <span class="pagination__page pagination__page--current" 
                          aria-current="page"
                          aria-label="{{ __('Page :page', ['page' => $page]) }}">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $url }}" 
                       class="pagination__page"
                       aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                        {{ $page }}
                    </a>
                @endif
            @endforeach
        </div>
        
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" 
               class="pagination__link pagination__link--next"
               rel="next"
               aria-label="{{ __('Go to next page') }}">
                {{ __('Next') }}
            </a>
        @else
            <span class="pagination__link pagination__link--disabled" aria-disabled="true">
                {{ __('Next') }}
            </span>
        @endif
        
        <div class="pagination__info" aria-live="polite">
            {{ __('Showing :from to :to of :total results', [
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ]) }}
        </div>
    </nav>
@endif
