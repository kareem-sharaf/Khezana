{{-- Filters: Panel (desktop) / Bottom sheet (mobile). Apply-only, no auto-submit. --}}
@php
    $currentFilters = $filters ?? [];
    $categories = $categories ?? collect();
    $activeFiltersCount = $activeFiltersCount ?? 0;
    $filterRoute = route('public.requests.index');
    $hasCategory = isset($currentFilters['category_id']);
@endphp

<div x-data="{
    filtersOpen: false,
    overlayReady: false,
    init() {
        this.$watch('filtersOpen', value => {
            if (value) {
                // Delay overlay activation to prevent immediate closing
                setTimeout(() => {
                    this.overlayReady = true;
                }, 100);
            } else {
                this.overlayReady = false;
            }
        });
    }
}">
    <button type="button" class="khezana-filters-toggle" aria-label="{{ __('common.ui.filters') }}"
        title="{{ __('common.ui.filters') }}" x-show="!filtersOpen" x-cloak @click.stop="filtersOpen = true"
        @touchstart.stop="filtersOpen = true" :aria-expanded="filtersOpen">
        <svg class="khezana-filters-toggle__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
        </svg>
        @if ($activeFiltersCount > 0)
            <span class="khezana-filters-toggle__badge">{{ $activeFiltersCount }}</span>
        @endif
    </button>

    <div class="khezana-filters-overlay" x-show="filtersOpen && overlayReady" x-transition:opacity.duration.200ms
        @click.self="filtersOpen = false" @mousedown.self="filtersOpen = false" @touchstart.self="filtersOpen = false"
        aria-hidden="true"></div>

    <aside class="khezana-filters" :class="{ 'is-active': filtersOpen }" x-show="filtersOpen"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full" @click.stop @mousedown.stop
        @touchstart.stop @keydown.escape.window="filtersOpen = false" role="dialog"
        aria-label="{{ __('common.ui.filters') }}">

        <div class="khezana-filters__drag" aria-hidden="true"></div>
        <header class="khezana-filters__header">
            <h2 class="khezana-filters__title">{{ __('common.ui.filters') }}</h2>
            <button type="button" class="khezana-filters__close" aria-label="{{ __('common.ui.close') }}"
                title="{{ __('common.ui.close') }}" @click.stop.prevent="filtersOpen = false">
                <svg class="khezana-filters__close-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    width="22" height="22" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </header>

        <form method="GET" action="{{ $filterRoute }}" class="khezana-filters__form" id="filters-form" @click.stop
            @mousedown.stop @touchstart.stop>
            @foreach (request()->except(['search', 'category_id', 'page']) as $paramKey => $paramVal)
                @if (is_array($paramVal))
                    @foreach ($paramVal as $v)
                        <input type="hidden" name="{{ $paramKey }}[]" value="{{ $v }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $paramKey }}" value="{{ $paramVal }}">
                @endif
            @endforeach

            <div class="khezana-filters__content">
                {{-- Category: accordion list --}}
                <section class="khezana-filter-section" x-data="{ open: {{ $hasCategory ? 'true' : 'false' }} }">
                    <button type="button" class="khezana-filter-section__trigger" @click="open = !open"
                        :aria-expanded="open">
                        <span class="khezana-filter-section__label">{{ __('requests.fields.category') }}</span>
                        <svg class="khezana-filter-section__chevron" :class="{ 'is-open': open }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="khezana-filter-section__body" x-show="open" x-transition.duration.200ms>
                        <div class="khezana-filter-list">
                            <label class="khezana-filter-list__item">
                                <input type="radio" name="category_id" value=""
                                    {{ !isset($currentFilters['category_id']) || $currentFilters['category_id'] === '' ? 'checked' : '' }}>
                                <span>{{ __('common.ui.all_categories') }}</span>
                            </label>
                            @foreach ($categories as $category)
                                @include('public.requests._partials._category-filter-item', [
                                    'category' => $category,
                                    'level' => 0,
                                    'currentFilters' => $currentFilters,
                                ])
                            @endforeach
                        </div>
                    </div>
                </section>
            </div>

            <footer class="khezana-filters__footer">
                <button type="submit"
                    class="khezana-btn khezana-btn-primary khezana-filters__apply">{{ __('common.ui.apply_filters') }}</button>
                <a href="{{ $filterRoute }}"
                    class="khezana-btn khezana-btn-secondary khezana-filters__reset">{{ __('common.ui.reset_filters') }}</a>
            </footer>
        </form>
    </aside>
</div>
