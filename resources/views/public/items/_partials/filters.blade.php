{{-- Filters Component for Items Listing --}}
{{-- Usage: @include('public.items._partials.filters', ['filters' => $filters, 'categories' => $categories, 'activeFiltersCount' => $activeFiltersCount]) --}}
{{-- Minimal JavaScript: Only Alpine.js for mobile toggle --}}

@php
    $currentFilters = $filters ?? [];
    $categories = $categories ?? collect();
    $activeFiltersCount = $activeFiltersCount ?? 0;
    $filterRoute = request()->routeIs('items.index') ? route('items.index') : route('public.items.index');
    
    // Build active filters for chips (calculated in Backend)
    $activeFiltersChips = [];
    if (isset($currentFilters['search']) && $currentFilters['search']) {
        $activeFiltersChips[] = [
            'key' => 'search',
            'label' => __('common.ui.search') . ': ' . $currentFilters['search'],
            'removeUrl' => $filterRoute . '?' . http_build_query(array_merge(request()->except(['search', 'page']), ['page' => 1])),
        ];
    }
    if (isset($currentFilters['category_id']) && $currentFilters['category_id']) {
        $category = $categories->flatten()->firstWhere('id', $currentFilters['category_id']);
        if ($category) {
            $activeFiltersChips[] = [
                'key' => 'category_id',
                'label' => __('items.fields.category') . ': ' . $category->name,
                'removeUrl' => $filterRoute . '?' . http_build_query(array_merge(request()->except(['category_id', 'page']), ['page' => 1])),
            ];
        }
    }
    if (isset($currentFilters['condition']) && $currentFilters['condition']) {
        $activeFiltersChips[] = [
            'key' => 'condition',
            'label' => __('items.fields.condition') . ': ' . __('items.conditions.' . $currentFilters['condition']),
            'removeUrl' => $filterRoute . '?' . http_build_query(array_merge(request()->except(['condition', 'page']), ['page' => 1])),
        ];
    }
    if (isset($currentFilters['price_min']) || isset($currentFilters['price_max'])) {
        $priceLabel = '';
        if (isset($currentFilters['price_min']) && isset($currentFilters['price_max'])) {
            $priceLabel = number_format($currentFilters['price_min'], 0) . ' - ' . number_format($currentFilters['price_max'], 0);
        } elseif (isset($currentFilters['price_min'])) {
            $priceLabel = __('common.ui.from') . ' ' . number_format($currentFilters['price_min'], 0);
        } elseif (isset($currentFilters['price_max'])) {
            $priceLabel = __('common.ui.to') . ' ' . number_format($currentFilters['price_max'], 0);
        }
        $activeFiltersChips[] = [
            'key' => 'price',
            'label' => __('items.fields.price') . ': ' . $priceLabel,
            'removeUrl' => $filterRoute . '?' . http_build_query(array_merge(request()->except(['price_min', 'price_max', 'page']), ['page' => 1])),
        ];
    }
    if (isset($currentFilters['operation_type']) && $currentFilters['operation_type']) {
        $activeFiltersChips[] = [
            'key' => 'operation_type',
            'label' => __('items.fields.operation_type') . ': ' . __('items.operation_types.' . $currentFilters['operation_type']),
            'removeUrl' => $filterRoute . '?' . http_build_query(array_merge(request()->except(['operation_type', 'page']), ['page' => 1])),
        ];
    }
@endphp

{{-- Mobile Filter Toggle Button --}}
<div x-data="{ filtersOpen: false }">
    <button 
        type="button" 
        class="khezana-filters-toggle" 
        aria-label="{{ __('common.ui.filters') }}"
        @click="filtersOpen = !filtersOpen"
        x-bind:aria-expanded="filtersOpen"
    >
        <span class="khezana-filters-toggle__icon">🔍</span>
        <span class="khezana-filters-toggle__text">{{ __('common.ui.filters') }}</span>
        @if($activeFiltersCount > 0)
            <span class="khezana-filters-toggle__badge">{{ $activeFiltersCount }}</span>
        @endif
    </button>

    {{-- Filters Overlay (Mobile) - Using Alpine --}}
    <div 
        class="khezana-filters-overlay" 
        x-show="filtersOpen"
        x-transition:opacity.duration.300ms
        @click="filtersOpen = false"
    ></div>

    {{-- Filters Sidebar --}}
    {{-- Desktop: Always visible, Mobile: Controlled by Alpine.js --}}
    <aside 
        class="khezana-filters" 
        x-bind:class="{ 'is-active': filtersOpen }"
        x-show="filtersOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        @keydown.escape.window="filtersOpen = false"
    >
        <div class="khezana-filters__header">
            <h2 class="khezana-filters__title">{{ __('common.ui.filters') }}</h2>
            <button 
                type="button" 
                class="khezana-filters__close" 
                aria-label="{{ __('common.ui.close') }}"
                @click="filtersOpen = false"
            >
                <span aria-hidden="true">×</span>
            </button>
        </div>

    @php
        $filterRoute = request()->routeIs('items.index') ? route('items.index') : route('public.items.index');
    @endphp

    {{-- Active Filters Chips --}}
    @if(count($activeFiltersChips) > 0)
    <div class="khezana-filters__active-chips">
        <div class="khezana-active-filters">
            <span class="khezana-active-filters__label">{{ __('common.ui.active_filters') }}:</span>
            <div class="khezana-active-filters__chips">
                @foreach($activeFiltersChips as $chip)
                <span class="khezana-filter-chip">
                    <span class="khezana-filter-chip__label">{{ $chip['label'] }}</span>
                    <a 
                        href="{{ $chip['removeUrl'] }}" 
                        class="khezana-filter-chip__remove" 
                        aria-label="{{ __('common.ui.remove_filter') }}"
                    >
                        <span aria-hidden="true">×</span>
                    </a>
                </span>
                @endforeach
                <a 
                    href="{{ $filterRoute . (request('operation_type') ? '?operation_type=' . request('operation_type') : '') }}" 
                    class="khezana-filter-chip khezana-filter-chip--clear-all"
                >
                    {{ __('common.ui.clear_all') }}
                </a>
            </div>
        </div>
    </div>
    @endif

    <form method="GET" action="{{ $filterRoute }}" class="khezana-filters__form">
        {{-- Preserve existing query parameters except filters --}}
        @if(request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
        @endif
        @if(request('per_page'))
            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
        @endif

        <div class="khezana-filters__content">
            {{-- Search Filter --}}
            <div class="khezana-filter-group">
                <label class="khezana-filter-group__label" for="filter_search">
                    {{ __('common.ui.search') }}
                </label>
                <input 
                    type="text" 
                    name="search" 
                    id="filter_search" 
                    class="khezana-filter-group__input"
                    placeholder="{{ __('common.ui.search_items') }}"
                    value="{{ $currentFilters['search'] ?? '' }}"
                >
            </div>

            {{-- Category Filter --}}
            <div class="khezana-filter-group">
                <label class="khezana-filter-group__label" for="filter_category">
                    {{ __('items.fields.category') }}
                </label>
                <select 
                    name="category_id" 
                    id="filter_category" 
                    class="khezana-filter-group__select"
                    onchange="this.form.submit()"
                >
                    <option value="">{{ __('common.ui.all_categories') }}</option>
                    @foreach($categories as $category)
                        <option 
                            value="{{ $category->id }}"
                            {{ (isset($currentFilters['category_id']) && $currentFilters['category_id'] == $category->id) ? 'selected' : '' }}
                        >
                            {{ $category->name }}
                        </option>
                        @if($category->children->count() > 0)
                            @foreach($category->children as $child)
                                <option 
                                    value="{{ $child->id }}"
                                    {{ (isset($currentFilters['category_id']) && $currentFilters['category_id'] == $child->id) ? 'selected' : '' }}
                                >
                                    &nbsp;&nbsp;{{ $child->name }}
                                </option>
                            @endforeach
                        @endif
                    @endforeach
                </select>
            </div>

            {{-- Condition Filter --}}
            <div class="khezana-filter-group">
                <label class="khezana-filter-group__label" for="filter_condition">
                    {{ __('items.fields.condition') }}
                </label>
                <select 
                    name="condition" 
                    id="filter_condition" 
                    class="khezana-filter-group__select"
                    onchange="this.form.submit()"
                >
                    <option value="">{{ __('common.ui.all_conditions') }}</option>
                    <option 
                        value="new"
                        {{ (isset($currentFilters['condition']) && $currentFilters['condition'] == 'new') ? 'selected' : '' }}
                    >
                        {{ __('items.conditions.new') }}
                    </option>
                    <option 
                        value="used"
                        {{ (isset($currentFilters['condition']) && $currentFilters['condition'] == 'used') ? 'selected' : '' }}
                    >
                        {{ __('items.conditions.used') }}
                    </option>
                </select>
            </div>

            {{-- Price Range Filter - Simplified with two inputs --}}
            <div class="khezana-filter-group">
                <label class="khezana-filter-group__label">
                    {{ __('items.fields.price') }}
                </label>
                <div class="khezana-filter-group__price-range">
                    <div class="khezana-price-range__input-group">
                        <label for="filter_price_min" class="khezana-price-range__label">{{ __('common.ui.min_price') }}</label>
                        <input 
                            type="number" 
                            name="price_min" 
                            id="filter_price_min" 
                            class="khezana-filter-group__input"
                            placeholder="0"
                            value="{{ $currentFilters['price_min'] ?? '' }}"
                            min="0"
                            step="1000"
                        >
                    </div>
                    <span class="khezana-filter-group__price-separator">-</span>
                    <div class="khezana-price-range__input-group">
                        <label for="filter_price_max" class="khezana-price-range__label">{{ __('common.ui.max_price') }}</label>
                        <input 
                            type="number" 
                            name="price_max" 
                            id="filter_price_max" 
                            class="khezana-filter-group__input"
                            placeholder="1,000,000"
                            value="{{ $currentFilters['price_max'] ?? '' }}"
                            min="0"
                            step="1000"
                        >
                    </div>
                </div>
            </div>

            {{-- Operation Type Filter (if not already set in URL) --}}
            @if(!request('operation_type'))
            <div class="khezana-filter-group">
                <label class="khezana-filter-group__label" for="filter_operation_type">
                    {{ __('items.fields.operation_type') }}
                </label>
                <select 
                    name="operation_type" 
                    id="filter_operation_type" 
                    class="khezana-filter-group__select"
                    onchange="this.form.submit()"
                >
                    <option value="">{{ __('common.ui.all_types') }}</option>
                    <option 
                        value="sell"
                        {{ (isset($currentFilters['operation_type']) && $currentFilters['operation_type'] == 'sell') ? 'selected' : '' }}
                    >
                        {{ __('items.operation_types.sell') }}
                    </option>
                    <option 
                        value="rent"
                        {{ (isset($currentFilters['operation_type']) && $currentFilters['operation_type'] == 'rent') ? 'selected' : '' }}
                    >
                        {{ __('items.operation_types.rent') }}
                    </option>
                    <option 
                        value="donate"
                        {{ (isset($currentFilters['operation_type']) && $currentFilters['operation_type'] == 'donate') ? 'selected' : '' }}
                    >
                        {{ __('items.operation_types.donate') }}
                    </option>
                </select>
            </div>
            @else
                <input type="hidden" name="operation_type" value="{{ request('operation_type') }}">
            @endif
        </div>

        <div class="khezana-filters__actions">
            <button type="submit" class="khezana-btn khezana-btn-primary khezana-filters__apply">
                {{ __('common.ui.apply_filters') }}
            </button>
            <a 
                href="{{ $filterRoute . (request('operation_type') ? '?operation_type=' . request('operation_type') : '') }}" 
                class="khezana-btn khezana-btn-secondary khezana-filters__reset"
            >
                {{ __('common.ui.reset_filters') }}
            </a>
        </div>
    </form>
    </aside>
</div>
