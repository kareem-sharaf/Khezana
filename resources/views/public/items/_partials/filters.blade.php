{{-- Modern Filters Component - Redesigned --}}
{{-- Usage: @include('public.items._partials.filters', ['filters' => $filters, 'categories' => $categories, 'activeFiltersCount' => $activeFiltersCount]) --}}

@php
    $currentFilters = $filters ?? [];
    $categories = $categories ?? collect();
    $activeFiltersCount = $activeFiltersCount ?? 0;
    $filterRoute = request()->routeIs('items.index') ? route('items.index') : route('public.items.index');
    
    // Flatten categories for checkbox list
    $flatCategories = $categories->flatMap(function ($c) {
        return collect([$c])->merge($c->children ?? collect());
    });
@endphp

{{-- Mobile Filter Toggle Button --}}
<div x-data="{ filtersOpen: false }">
    <button type="button" class="khezana-filters-toggle" aria-label="{{ __('common.ui.filters') }}"
        @click="filtersOpen = !filtersOpen" x-bind:aria-expanded="filtersOpen">
        <svg class="khezana-filters-toggle__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
        </svg>
        <span class="khezana-filters-toggle__text">{{ __('common.ui.filters') }}</span>
        @if ($activeFiltersCount > 0)
            <span class="khezana-filters-toggle__badge">{{ $activeFiltersCount }}</span>
        @endif
    </button>

    {{-- Filters Overlay (Mobile) --}}
    <div class="khezana-filters-overlay" x-show="filtersOpen" x-transition:opacity.duration.200ms
        @click="filtersOpen = false"></div>

    {{-- Filters Sidebar --}}
    <aside class="khezana-filters" x-bind:class="{ 'is-active': filtersOpen }" x-show="filtersOpen"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
        @keydown.escape.window="filtersOpen = false">
        
        <div class="khezana-filters__header">
            <h2 class="khezana-filters__title">{{ __('common.ui.filters') }}</h2>
            <button type="button" class="khezana-filters__close" aria-label="{{ __('common.ui.close') }}"
                @click="filtersOpen = false">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="GET" action="{{ $filterRoute }}" class="khezana-filters__form" id="filters-form">
            {{-- Preserve existing query parameters --}}
            @foreach (request()->except(['search', 'category_id', 'condition', 'price_min', 'price_max', 'operation_type', 'page']) as $paramKey => $paramVal)
                @if (is_array($paramVal))
                    @foreach ($paramVal as $v)
                        <input type="hidden" name="{{ $paramKey }}[]" value="{{ $v }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $paramKey }}" value="{{ $paramVal }}">
                @endif
            @endforeach

            <div class="khezana-filters__content">
                {{-- Operation Type Filter - Toggle Buttons --}}
                <div class="khezana-filter-section" x-data="{ open: {{ isset($currentFilters['operation_type']) && $currentFilters['operation_type'] ? 'true' : 'true' }} }">
                    <button type="button" class="khezana-filter-section__header" @click="open = !open">
                        <span class="khezana-filter-section__title">{{ __('items.fields.operation_type') }}</span>
                        <svg class="khezana-filter-section__icon" :class="{ 'is-open': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="khezana-filter-section__content" x-show="open" x-transition>
                        <div class="khezana-toggle-group">
                            <label class="khezana-toggle-option">
                                <input type="radio" name="operation_type" value="" 
                                    {{ !isset($currentFilters['operation_type']) || $currentFilters['operation_type'] == '' ? 'checked' : '' }}>
                                <span class="khezana-toggle-option__label">{{ __('common.ui.all_types') }}</span>
                            </label>
                            <label class="khezana-toggle-option">
                                <input type="radio" name="operation_type" value="sell"
                                    {{ isset($currentFilters['operation_type']) && $currentFilters['operation_type'] == 'sell' ? 'checked' : '' }}>
                                <span class="khezana-toggle-option__label">{{ __('items.operation_types.sell') }}</span>
                            </label>
                            <label class="khezana-toggle-option">
                                <input type="radio" name="operation_type" value="rent"
                                    {{ isset($currentFilters['operation_type']) && $currentFilters['operation_type'] == 'rent' ? 'checked' : '' }}>
                                <span class="khezana-toggle-option__label">{{ __('items.operation_types.rent') }}</span>
                            </label>
                            <label class="khezana-toggle-option">
                                <input type="radio" name="operation_type" value="donate"
                                    {{ isset($currentFilters['operation_type']) && $currentFilters['operation_type'] == 'donate' ? 'checked' : '' }}>
                                <span class="khezana-toggle-option__label">{{ __('items.operation_types.donate') }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Category Filter - Checkboxes with Accordion --}}
                <div class="khezana-filter-section" x-data="{ open: {{ isset($currentFilters['category_id']) ? 'true' : 'false' }} }">
                    <button type="button" class="khezana-filter-section__header" @click="open = !open">
                        <span class="khezana-filter-section__title">{{ __('items.fields.category') }}</span>
                        <svg class="khezana-filter-section__icon" :class="{ 'is-open': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="khezana-filter-section__content" x-show="open" x-transition>
                        <div class="khezana-checkbox-group">
                            <label class="khezana-checkbox-option">
                                <input type="radio" name="category_id" value=""
                                    {{ !isset($currentFilters['category_id']) || $currentFilters['category_id'] == '' ? 'checked' : '' }}>
                                <span class="khezana-checkbox-option__check"></span>
                                <span class="khezana-checkbox-option__label">{{ __('common.ui.all_categories') }}</span>
                            </label>
                            @foreach ($categories as $category)
                                <label class="khezana-checkbox-option">
                                    <input type="radio" name="category_id" value="{{ $category->id }}"
                                        {{ isset($currentFilters['category_id']) && $currentFilters['category_id'] == $category->id ? 'checked' : '' }}>
                                    <span class="khezana-checkbox-option__check"></span>
                                    <span class="khezana-checkbox-option__label">{{ $category->name }}</span>
                                </label>
                                @if ($category->children && $category->children->count() > 0)
                                    @foreach ($category->children as $child)
                                        <label class="khezana-checkbox-option khezana-checkbox-option--child">
                                            <input type="radio" name="category_id" value="{{ $child->id }}"
                                                {{ isset($currentFilters['category_id']) && $currentFilters['category_id'] == $child->id ? 'checked' : '' }}>
                                            <span class="khezana-checkbox-option__check"></span>
                                            <span class="khezana-checkbox-option__label">{{ $child->name }}</span>
                                        </label>
                                    @endforeach
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Condition Filter - Pills --}}
                <div class="khezana-filter-section" x-data="{ open: {{ isset($currentFilters['condition']) ? 'true' : 'false' }} }">
                    <button type="button" class="khezana-filter-section__header" @click="open = !open">
                        <span class="khezana-filter-section__title">{{ __('items.fields.condition') }}</span>
                        <svg class="khezana-filter-section__icon" :class="{ 'is-open': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="khezana-filter-section__content" x-show="open" x-transition>
                        <div class="khezana-pill-group">
                            <label class="khezana-pill">
                                <input type="radio" name="condition" value=""
                                    {{ !isset($currentFilters['condition']) || $currentFilters['condition'] == '' ? 'checked' : '' }}>
                                <span class="khezana-pill__label">{{ __('common.ui.all_conditions') }}</span>
                            </label>
                            <label class="khezana-pill">
                                <input type="radio" name="condition" value="new"
                                    {{ isset($currentFilters['condition']) && $currentFilters['condition'] == 'new' ? 'checked' : '' }}>
                                <span class="khezana-pill__label">{{ __('items.conditions.new') }}</span>
                            </label>
                            <label class="khezana-pill">
                                <input type="radio" name="condition" value="used"
                                    {{ isset($currentFilters['condition']) && $currentFilters['condition'] == 'used' ? 'checked' : '' }}>
                                <span class="khezana-pill__label">{{ __('items.conditions.used') }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Price Range Filter - Simple Slider --}}
                <div class="khezana-filter-section" 
                    x-data="{ 
                        open: {{ (isset($currentFilters['price_min']) || isset($currentFilters['price_max'])) ? 'true' : 'false' }},
                        ...priceSlider({{ isset($currentFilters['price_min']) && $currentFilters['price_min'] ? (int) $currentFilters['price_min'] : 0 }}, {{ isset($currentFilters['price_max']) && $currentFilters['price_max'] ? (int) $currentFilters['price_max'] : 1000000 }})
                    }">
                    <button type="button" class="khezana-filter-section__header" @click="open = !open">
                        <span class="khezana-filter-section__title">{{ __('items.fields.price') }}</span>
                        <svg class="khezana-filter-section__icon" :class="{ 'is-open': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="khezana-filter-section__content" x-show="open" x-transition>
                        <div class="khezana-price-slider-wrapper"
                            :class="{ 'khezana-price-slider-wrapper--dragging-min': isDraggingMin, 'khezana-price-slider-wrapper--dragging-max': isDraggingMax }">
                            <div class="khezana-price-slider">
                                <div class="khezana-price-slider__track" @click="onTrackClick($event)">
                                    <div class="khezana-price-slider__range"
                                        :style="'left: ' + (minPercent) + '%; width: ' + (maxPercent - minPercent) + '%'">
                                    </div>
                                </div>
                                <input type="range" min="0" max="1000000" step="1000" :value="minValue"
                                    @input="updateMin($event.target.value)" @mousedown="onDragStart('min')"
                                    @mouseup="onDragEnd('min')" @touchstart="onDragStart('min')"
                                    @touchend="onDragEnd('min')"
                                    class="khezana-price-slider__input khezana-price-slider__input--min">
                                <input type="range" min="0" max="1000000" step="1000"
                                    :value="maxValue" @input="updateMax($event.target.value)"
                                    @mousedown="onDragStart('max')" @mouseup="onDragEnd('max')"
                                    @touchstart="onDragStart('max')" @touchend="onDragEnd('max')"
                                    class="khezana-price-slider__input khezana-price-slider__input--max">
                            </div>
                            <div class="khezana-price-slider__values">
                                <span class="khezana-price-slider__value" x-text="formatPrice(minValue)">0</span>
                                <span class="khezana-price-slider__value" x-text="formatPrice(maxValue)">1,000,000</span>
                            </div>
                        </div>
                        <input type="hidden" name="price_min" :value="minValue">
                        <input type="hidden" name="price_max" :value="maxValue">
                    </div>
                </div>
            </div>

            <div class="khezana-filters__actions">
                <button type="submit" class="khezana-btn khezana-btn-primary khezana-filters__apply">
                    {{ __('common.ui.apply_filters') }}
                </button>
                <a href="{{ $filterRoute }}"
                    class="khezana-btn khezana-btn-secondary khezana-filters__reset">
                    {{ __('common.ui.reset_filters') }}
                </a>
            </div>
        </form>
    </aside>
</div>
