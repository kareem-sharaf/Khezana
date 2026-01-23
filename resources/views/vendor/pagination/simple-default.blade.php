@if ($paginator->hasPages())
    <nav class="khezana-pagination khezana-pagination--simple" aria-label="{{ __('common.ui.pagination') }}">
        <ul class="khezana-pagination__list">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="khezana-pagination__item khezana-pagination__item--disabled" aria-disabled="true">
                    <span class="khezana-pagination__link khezana-pagination__link--disabled" aria-hidden="true">
                        <span class="khezana-pagination__icon">‹</span>
                        <span class="khezana-pagination__text">{{ __('common.ui.previous') }}</span>
                    </span>
                </li>
            @else
                <li class="khezana-pagination__item">
                    <a href="{{ $paginator->previousPageUrl() }}" 
                       class="khezana-pagination__link khezana-pagination__link--prev" 
                       rel="prev">
                        <span class="khezana-pagination__icon">‹</span>
                        <span class="khezana-pagination__text">{{ __('common.ui.previous') }}</span>
                    </a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="khezana-pagination__item">
                    <a href="{{ $paginator->nextPageUrl() }}" 
                       class="khezana-pagination__link khezana-pagination__link--next" 
                       rel="next">
                        <span class="khezana-pagination__text">{{ __('common.ui.next') }}</span>
                        <span class="khezana-pagination__icon">›</span>
                    </a>
                </li>
            @else
                <li class="khezana-pagination__item khezana-pagination__item--disabled" aria-disabled="true">
                    <span class="khezana-pagination__link khezana-pagination__link--disabled" aria-hidden="true">
                        <span class="khezana-pagination__text">{{ __('common.ui.next') }}</span>
                        <span class="khezana-pagination__icon">›</span>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
