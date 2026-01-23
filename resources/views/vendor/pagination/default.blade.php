@if ($paginator->hasPages())
    <nav class="khezana-pagination" aria-label="{{ __('common.ui.pagination') }}">
        <ul class="khezana-pagination__list">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="khezana-pagination__item khezana-pagination__item--disabled" aria-disabled="true" aria-label="{{ __('common.ui.previous') }}">
                    <span class="khezana-pagination__link khezana-pagination__link--disabled" aria-hidden="true">
                        <span class="khezana-pagination__icon">‹</span>
                        <span class="khezana-pagination__text">{{ __('common.ui.previous') }}</span>
                    </span>
                </li>
            @else
                <li class="khezana-pagination__item">
                    <a href="{{ $paginator->previousPageUrl() }}" 
                       class="khezana-pagination__link khezana-pagination__link--prev" 
                       rel="prev" 
                       aria-label="{{ __('common.ui.previous') }}">
                        <span class="khezana-pagination__icon">‹</span>
                        <span class="khezana-pagination__text">{{ __('common.ui.previous') }}</span>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="khezana-pagination__item khezana-pagination__item--ellipsis" aria-disabled="true">
                        <span class="khezana-pagination__ellipsis">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="khezana-pagination__item khezana-pagination__item--active" aria-current="page">
                                <span class="khezana-pagination__link khezana-pagination__link--active">{{ $page }}</span>
                            </li>
                        @else
                            <li class="khezana-pagination__item">
                                <a href="{{ $url }}" class="khezana-pagination__link">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="khezana-pagination__item">
                    <a href="{{ $paginator->nextPageUrl() }}" 
                       class="khezana-pagination__link khezana-pagination__link--next" 
                       rel="next" 
                       aria-label="{{ __('common.ui.next') }}">
                        <span class="khezana-pagination__text">{{ __('common.ui.next') }}</span>
                        <span class="khezana-pagination__icon">›</span>
                    </a>
                </li>
            @else
                <li class="khezana-pagination__item khezana-pagination__item--disabled" aria-disabled="true" aria-label="{{ __('common.ui.next') }}">
                    <span class="khezana-pagination__link khezana-pagination__link--disabled" aria-hidden="true">
                        <span class="khezana-pagination__text">{{ __('common.ui.next') }}</span>
                        <span class="khezana-pagination__icon">›</span>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
