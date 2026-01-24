{{-- Active filter chips above results. Shown only when filters are active. --}}
@php
    $currentFilters = $filters ?? [];
    $categories = $categories ?? collect();
    $filterRoute = route('public.requests.index');
    $baseQuery = request()->except(['page', 'category_id', 'search']);
    $flatCats = $categories->flatMap(fn($c) => collect([$c])->merge($c->children ?? []))->keyBy('id');
    $queryWithout = function ($keys) use ($baseQuery, $currentFilters, $filterRoute) {
        $f = $currentFilters;
        foreach ((array) $keys as $k) {
            unset($f[$k]);
        }
        return $filterRoute . '?' . http_build_query(array_filter(array_merge($baseQuery, $f)));
    };
@endphp
@if (!empty($currentFilters))
    <div class="khezana-filter-chips" role="list" aria-label="{{ __('common.ui.active_filters') }}">
        @if (isset($currentFilters['category_id']))
            @php
                $cat = $flatCats->get((int) $currentFilters['category_id']);
                $label = $cat ? $cat->name : __('requests.fields.category');
            @endphp
            <span class="khezana-filter-chip" role="listitem">
                <span class="khezana-filter-chip__label">{{ $label }}</span>
                <a href="{{ $queryWithout('category_id') }}" class="khezana-filter-chip__remove"
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
