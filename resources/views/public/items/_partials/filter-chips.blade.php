{{-- Active filter chips above results. Shown only when filters are active. --}}
@php
    use App\Models\Setting;
    $currentFilters = $filters ?? [];
    $categories = $categories ?? collect();
    $filterRoute = request()->routeIs('items.index') ? route('items.index') : route('public.items.index');
    $baseQuery = request()->except([
        'page',
        'category_id',
        'condition',
        'operation_type',
        'price_min',
        'price_max',
        'search',
    ]);
    $flatCats = $categories->flatMap(fn($c) => collect([$c])->merge($c->children ?? []))->keyBy('id');
    $queryWithout = function ($keys) use ($baseQuery, $currentFilters, $filterRoute) {
        $f = $currentFilters;
        foreach ((array) $keys as $k) {
            unset($f[$k]);
        }
        return $filterRoute . '?' . http_build_query(array_filter(array_merge($baseQuery, $f)));
    };
    // Get price slider max from settings
    $sliderMax = (int) Setting::priceSliderMax();
@endphp
@if (!empty($currentFilters))
    <div class="khezana-filter-chips" role="list" aria-label="{{ __('common.ui.active_filters') }}">
        @if (isset($currentFilters['operation_type']))
            <span class="khezana-filter-chip" role="listitem">
                <span
                    class="khezana-filter-chip__label">{{ __('items.operation_types.' . $currentFilters['operation_type']) }}</span>
                <a href="{{ $queryWithout('operation_type') }}" class="khezana-filter-chip__remove"
                    aria-label="{{ __('common.actions.remove') }}">×</a>
            </span>
        @endif
        @if (isset($currentFilters['category_id']))
            @php
                $cat = $flatCats->get((int) $currentFilters['category_id']);
                $label = $cat ? $cat->name : __('items.fields.category');
            @endphp
            <span class="khezana-filter-chip" role="listitem">
                <span class="khezana-filter-chip__label">{{ $label }}</span>
                <a href="{{ $queryWithout('category_id') }}" class="khezana-filter-chip__remove"
                    aria-label="{{ __('common.actions.remove') }}">×</a>
            </span>
        @endif
        @if (isset($currentFilters['condition']))
            <span class="khezana-filter-chip" role="listitem">
                <span
                    class="khezana-filter-chip__label">{{ __('items.conditions.' . $currentFilters['condition']) }}</span>
                <a href="{{ $queryWithout('condition') }}" class="khezana-filter-chip__remove"
                    aria-label="{{ __('common.actions.remove') }}">×</a>
            </span>
        @endif
        @if (
            (isset($currentFilters['price_min']) && (int) $currentFilters['price_min'] > 0) ||
                (isset($currentFilters['price_max']) && (int) $currentFilters['price_max'] < $sliderMax))
            @php
                $locale = app()->getLocale();
                $sliderMin = (int) Setting::priceSliderMin();
                $min = isset($currentFilters['price_min']) ? (int) $currentFilters['price_min'] : $sliderMin;
                $max = isset($currentFilters['price_max']) ? (int) $currentFilters['price_max'] : $sliderMax;
                $label =
                    \Illuminate\Support\Number::format($min, locale: $locale) .
                    ' – ' .
                    \Illuminate\Support\Number::format($max, locale: $locale);
            @endphp
            <span class="khezana-filter-chip" role="listitem">
                <span class="khezana-filter-chip__label">{{ $label }}</span>
                <a href="{{ $queryWithout(['price_min', 'price_max']) }}" class="khezana-filter-chip__remove"
                    aria-label="{{ __('common.actions.remove') }}">×</a>
            </span>
        @endif
        @if (!empty($currentFilters['search']))
            <span class="khezana-filter-chip" role="listitem">
                <span class="khezana-filter-chip__label">{{ __('common.ui.search') }}:
                    {{ Str::limit($currentFilters['search'], 20) }}</span>
                <a href="{{ $queryWithout('search') }}" class="khezana-filter-chip__remove"
                    aria-label="{{ __('common.actions.remove') }}">×</a>
            </span>
        @endif
        <a href="{{ $filterRoute }}"
            class="khezana-filter-chip khezana-filter-chip--clear">{{ __('common.ui.clear_all') }}</a>
    </div>
@endif
