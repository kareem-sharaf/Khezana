{{-- Filters Component for Items Listing --}}
{{-- Usage: @include('public.items._partials.filters', ['filters' => $filters, 'categories' => $categories]) --}}

@php
    $currentFilters = $filters ?? [];
    $categories = $categories ?? collect();
@endphp

{{-- Mobile Filter Toggle Button --}}
<button 
    type="button" 
    class="khezana-filters-toggle" 
    aria-label="{{ __('common.ui.filters') }}"
    aria-expanded="false"
    data-filters-toggle
>
    <span class="khezana-filters-toggle__icon">🔍</span>
    <span class="khezana-filters-toggle__text">{{ __('common.ui.filters') }}</span>
    @if(count($currentFilters) > 0)
        <span class="khezana-filters-toggle__badge">{{ count($currentFilters) }}</span>
    @endif
</button>

{{-- Filters Sidebar --}}
<aside class="khezana-filters" data-filters>
    <div class="khezana-filters__header">
        <h2 class="khezana-filters__title">{{ __('common.ui.filters') }}</h2>
        <button 
            type="button" 
            class="khezana-filters__close" 
            aria-label="{{ __('common.ui.close') }}"
            data-filters-close
        >
            <span aria-hidden="true">×</span>
        </button>
    </div>

    @php
        $filterRoute = request()->routeIs('items.index') ? route('items.index') : route('public.items.index');
        
        // Build active filters for chips
        $activeFiltersChips = [];
        if (isset($currentFilters['search']) && $currentFilters['search']) {
            $activeFiltersChips[] = [
                'key' => 'search',
                'label' => __('common.ui.search') . ': ' . $currentFilters['search'],
                'value' => $currentFilters['search'],
            ];
        }
        if (isset($currentFilters['category_id']) && $currentFilters['category_id']) {
            $category = $categories->flatten()->firstWhere('id', $currentFilters['category_id']);
            if ($category) {
                $activeFiltersChips[] = [
                    'key' => 'category_id',
                    'label' => __('items.fields.category') . ': ' . $category->name,
                    'value' => $currentFilters['category_id'],
                ];
            }
        }
        if (isset($currentFilters['condition']) && $currentFilters['condition']) {
            $activeFiltersChips[] = [
                'key' => 'condition',
                'label' => __('items.fields.condition') . ': ' . __('items.conditions.' . $currentFilters['condition']),
                'value' => $currentFilters['condition'],
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
                'value' => 'price',
            ];
        }
        if (isset($currentFilters['operation_type']) && $currentFilters['operation_type']) {
            $activeFiltersChips[] = [
                'key' => 'operation_type',
                'label' => __('items.fields.operation_type') . ': ' . __('items.operation_types.' . $currentFilters['operation_type']),
                'value' => $currentFilters['operation_type'],
            ];
        }
    @endphp

    {{-- Active Filters Chips --}}
    @if(count($activeFiltersChips) > 0)
    <div class="khezana-filters__active-chips">
        <div class="khezana-active-filters">
            <span class="khezana-active-filters__label">{{ __('common.ui.active_filters') }}:</span>
            <div class="khezana-active-filters__chips">
                @foreach($activeFiltersChips as $chip)
                <span class="khezana-filter-chip" data-filter-chip="{{ $chip['key'] }}">
                    <span class="khezana-filter-chip__label">{{ $chip['label'] }}</span>
                    <button 
                        type="button" 
                        class="khezana-filter-chip__remove" 
                        aria-label="{{ __('common.ui.remove_filter') }}"
                        data-filter-remove="{{ $chip['key'] }}"
                        data-filter-value="{{ $chip['value'] }}"
                    >
                        <span aria-hidden="true">×</span>
                    </button>
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

    <form method="GET" action="{{ $filterRoute }}" class="khezana-filters__form" data-filters-form>
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

            {{-- Price Range Filter with Slider --}}
            <div class="khezana-filter-group">
                <label class="khezana-filter-group__label">
                    {{ __('items.fields.price') }}
                </label>
                <div class="khezana-filter-group__price-range">
                    <input 
                        type="number" 
                        name="price_min" 
                        id="filter_price_min" 
                        class="khezana-filter-group__input khezana-filter-group__price-input"
                        placeholder="{{ __('common.ui.min_price') }}"
                        value="{{ $currentFilters['price_min'] ?? '' }}"
                        min="0"
                        step="0.01"
                        data-price-min
                    >
                    <span class="khezana-filter-group__price-separator">-</span>
                    <input 
                        type="number" 
                        name="price_max" 
                        id="filter_price_max" 
                        class="khezana-filter-group__input khezana-filter-group__price-input"
                        placeholder="{{ __('common.ui.max_price') }}"
                        value="{{ $currentFilters['price_max'] ?? '' }}"
                        min="0"
                        step="0.01"
                        data-price-max
                    >
                </div>
                <div class="khezana-filter-group__price-slider" data-price-slider>
                    <div class="khezana-price-slider">
                        <div class="khezana-price-slider__track">
                            <div class="khezana-price-slider__range" data-price-range></div>
                        </div>
                        <input 
                            type="range" 
                            class="khezana-price-slider__input khezana-price-slider__input--min" 
                            min="0" 
                            max="1000000" 
                            step="1000"
                            value="{{ $currentFilters['price_min'] ?? 0 }}"
                            data-slider-min
                        >
                        <input 
                            type="range" 
                            class="khezana-price-slider__input khezana-price-slider__input--max" 
                            min="0" 
                            max="1000000" 
                            step="1000"
                            value="{{ $currentFilters['price_max'] ?? 1000000 }}"
                            data-slider-max
                        >
                    </div>
                    <div class="khezana-filter-group__price-display">
                        <span data-price-display-min>{{ number_format($currentFilters['price_min'] ?? 0, 0) }}</span>
                        <span> - </span>
                        <span data-price-display-max>{{ number_format($currentFilters['price_max'] ?? 1000000, 0) }}</span>
                        <span class="khezana-filter-group__price-currency">{{ __('common.ui.currency') }}</span>
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

{{-- Filters Overlay (Mobile) --}}
<div class="khezana-filters-overlay" data-filters-overlay></div>
