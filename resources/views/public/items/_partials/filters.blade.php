{{-- Filters: Panel (desktop) / Bottom sheet (mobile). Apply-only, no auto-submit. --}}
@php
    use App\Models\Setting;
    use App\Models\Branch;
    $currentFilters = $filters ?? [];
    $categories = $categories ?? collect();
    $branches = Branch::active()->orderBy('name')->get();
    $activeFiltersCount = $activeFiltersCount ?? 0;
    $filterRoute = request()->routeIs('items.index') ? route('items.index') : route('public.items.index');
    $hasCategory = isset($currentFilters['category_id']);
    $hasBranch = isset($currentFilters['branch_id']);
    $hasPrice = isset($currentFilters['price_min']) || isset($currentFilters['price_max']);
    
    // Get price slider settings from admin settings
    $sliderMin = (int) Setting::priceSliderMin();
    $sliderMax = (int) Setting::priceSliderMax();
    $sliderStep = (int) Setting::priceSliderStep();
    $sliderMinGap = (int) Setting::priceSliderMinGap();
    
    // Use current filter values or default to slider min/max
    $priceMin = isset($currentFilters['price_min']) ? (int) $currentFilters['price_min'] : $sliderMin;
    $priceMax = isset($currentFilters['price_max']) ? (int) $currentFilters['price_max'] : $sliderMax;
    
    // Ensure values are within slider range
    $priceMin = max($sliderMin, min($sliderMax, $priceMin));
    $priceMax = max($sliderMin, min($sliderMax, $priceMax));
@endphp

<div x-data="{ 
    filtersOpen: false,
    overlayReady: false,
    operationType: '{{ $currentFilters['operation_type'] ?? '' }}',
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
        title="{{ __('common.ui.filters') }}"
        x-show="!filtersOpen"
        x-cloak
        @click.stop="filtersOpen = true"
        @touchstart.stop="filtersOpen = true"
        :aria-expanded="filtersOpen">
        <svg class="khezana-filters-toggle__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
        </svg>
        @if ($activeFiltersCount > 0)
            <span class="khezana-filters-toggle__badge">{{ $activeFiltersCount }}</span>
        @endif
    </button>

    <div class="khezana-filters-overlay" 
        x-show="filtersOpen && overlayReady" 
        x-transition:opacity.duration.200ms
        @click.self="filtersOpen = false" 
        @mousedown.self="filtersOpen = false"
        @touchstart.self="filtersOpen = false"
        aria-hidden="true"></div>

    <aside class="khezana-filters" :class="{ 'is-active': filtersOpen }" x-show="filtersOpen"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
        @click.stop
        @mousedown.stop
        @touchstart.stop
        @keydown.escape.window="filtersOpen = false" role="dialog" aria-label="{{ __('common.ui.filters') }}">

        <div class="khezana-filters__drag" aria-hidden="true"></div>
        <header class="khezana-filters__header">
            <h2 class="khezana-filters__title">{{ __('common.ui.filters') }}</h2>
            <button type="button" class="khezana-filters__close" aria-label="{{ __('common.ui.close') }}"
                title="{{ __('common.ui.close') }}"
                @click.stop.prevent="filtersOpen = false">
                <svg class="khezana-filters__close-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </header>

        <form method="GET" action="{{ $filterRoute }}" class="khezana-filters__form" id="filters-form"
            @click.stop
            @mousedown.stop
            @touchstart.stop>
            @foreach (request()->except(['search', 'category_id', 'condition', 'price_min', 'price_max', 'operation_type', 'branch_id', 'page']) as $paramKey => $paramVal)
                @if (is_array($paramVal))
                    @foreach ($paramVal as $v)
                        <input type="hidden" name="{{ $paramKey }}[]" value="{{ $v }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $paramKey }}" value="{{ $paramVal }}">
                @endif
            @endforeach

            <div class="khezana-filters__content">
                {{-- Operation type: segmented --}}
                <section class="khezana-filter-section" x-data="{ open: true }">
                    <button type="button" class="khezana-filter-section__trigger" @click="open = !open"
                        :aria-expanded="open">
                        <span class="khezana-filter-section__label">{{ __('items.fields.operation_type') }}</span>
                        <svg class="khezana-filter-section__chevron" :class="{ 'is-open': open }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="khezana-filter-section__body" x-show="open" x-transition.duration.200ms>
                        <div class="khezana-segmented">
                            <label class="khezana-segmented__option">
                                <input type="radio" name="operation_type" value=""
                                    {{ !isset($currentFilters['operation_type']) || $currentFilters['operation_type'] === '' ? 'checked' : '' }}
                                    @change="operationType = $event.target.value">
                                <span>{{ __('common.ui.all_types') }}</span>
                            </label>
                            <label class="khezana-segmented__option">
                                <input type="radio" name="operation_type" value="sell"
                                    {{ isset($currentFilters['operation_type']) && $currentFilters['operation_type'] === 'sell' ? 'checked' : '' }}
                                    @change="operationType = $event.target.value">
                                <span>{{ __('items.operation_types.sell') }}</span>
                            </label>
                            <label class="khezana-segmented__option">
                                <input type="radio" name="operation_type" value="rent"
                                    {{ isset($currentFilters['operation_type']) && $currentFilters['operation_type'] === 'rent' ? 'checked' : '' }}
                                    @change="operationType = $event.target.value">
                                <span>{{ __('items.operation_types.rent') }}</span>
                            </label>
                            <label class="khezana-segmented__option">
                                <input type="radio" name="operation_type" value="donate"
                                    {{ isset($currentFilters['operation_type']) && $currentFilters['operation_type'] === 'donate' ? 'checked' : '' }}
                                    @change="operationType = $event.target.value">
                                <span>{{ __('items.operation_types.donate') }}</span>
                            </label>
                        </div>
                    </div>
                </section>

                {{-- Category: accordion list --}}
                <section class="khezana-filter-section" x-data="{ open: {{ $hasCategory ? 'true' : 'false' }} }">
                    <button type="button" class="khezana-filter-section__trigger" @click="open = !open"
                        :aria-expanded="open">
                        <span class="khezana-filter-section__label">{{ __('items.fields.category') }}</span>
                        <svg class="khezana-filter-section__chevron" :class="{ 'is-open': open }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
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
                                <label class="khezana-filter-list__item">
                                    <input type="radio" name="category_id" value="{{ $category->id }}"
                                        {{ isset($currentFilters['category_id']) && (int) $currentFilters['category_id'] === $category->id ? 'checked' : '' }}>
                                    <span>{{ $category->name }}</span>
                                </label>
                                @if ($category->children && $category->children->count() > 0)
                                    @foreach ($category->children as $child)
                                        <label class="khezana-filter-list__item khezana-filter-list__item--child">
                                            <input type="radio" name="category_id" value="{{ $child->id }}"
                                                {{ isset($currentFilters['category_id']) && (int) $currentFilters['category_id'] === $child->id ? 'checked' : '' }}>
                                            <span>{{ $child->name }}</span>
                                        </label>
                                    @endforeach
                                @endif
                            @endforeach
                        </div>
                    </div>
                </section>

                {{-- Condition: pills --}}
                <section class="khezana-filter-section" x-data="{ open: {{ isset($currentFilters['condition']) ? 'true' : 'false' }} }">
                    <button type="button" class="khezana-filter-section__trigger" @click="open = !open"
                        :aria-expanded="open">
                        <span class="khezana-filter-section__label">{{ __('items.fields.condition') }}</span>
                        <svg class="khezana-filter-section__chevron" :class="{ 'is-open': open }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="khezana-filter-section__body" x-show="open" x-transition.duration.200ms>
                        <div class="khezana-pills">
                            <label class="khezana-pill">
                                <input type="radio" name="condition" value=""
                                    {{ !isset($currentFilters['condition']) || $currentFilters['condition'] === '' ? 'checked' : '' }}>
                                <span>{{ __('common.ui.all_conditions') }}</span>
                            </label>
                            <label class="khezana-pill">
                                <input type="radio" name="condition" value="new"
                                    {{ isset($currentFilters['condition']) && $currentFilters['condition'] === 'new' ? 'checked' : '' }}>
                                <span>{{ __('items.conditions.new') }}</span>
                            </label>
                            <label class="khezana-pill">
                                <input type="radio" name="condition" value="used"
                                    {{ isset($currentFilters['condition']) && $currentFilters['condition'] === 'used' ? 'checked' : '' }}>
                                <span>{{ __('items.conditions.used') }}</span>
                            </label>
                        </div>
                    </div>
                </section>

                {{-- Branch: dropdown --}}
                @if ($branches->count() > 0)
                <section class="khezana-filter-section" x-data="{ open: {{ $hasBranch ? 'true' : 'false' }} }">
                    <button type="button" class="khezana-filter-section__trigger" @click="open = !open"
                        :aria-expanded="open">
                        <span class="khezana-filter-section__label">📍 {{ __('items.branch') }}</span>
                        <svg class="khezana-filter-section__chevron" :class="{ 'is-open': open }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="khezana-filter-section__body" x-show="open" x-transition.duration.200ms>
                        <div class="khezana-filter-list">
                            <label class="khezana-filter-list__item">
                                <input type="radio" name="branch_id" value=""
                                    {{ !isset($currentFilters['branch_id']) || $currentFilters['branch_id'] === '' ? 'checked' : '' }}>
                                <span>{{ __('common.ui.all_branches') }}</span>
                            </label>
                            @foreach ($branches as $branch)
                                <label class="khezana-filter-list__item">
                                    <input type="radio" name="branch_id" value="{{ $branch->id }}"
                                        {{ isset($currentFilters['branch_id']) && (int) $currentFilters['branch_id'] === $branch->id ? 'checked' : '' }}>
                                    <span>{{ $branch->name }} - {{ $branch->city }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </section>
                @endif

                {{-- Price: simple slider --}}
                <section class="khezana-filter-section" 
                    x-data="{
                        open: {{ $hasPrice ? 'true' : 'false' }},
                        ...priceSlider({{ $priceMin }}, {{ $priceMax }}, {
                            min: {{ $sliderMin }},
                            max: {{ $sliderMax }},
                            step: {{ $sliderStep }},
                            minGap: {{ $sliderMinGap }}
                        })
                    }"
                    x-show="!operationType || operationType !== 'donate'"
                    x-transition>
                    <button type="button" class="khezana-filter-section__trigger" @click="open = !open"
                        :aria-expanded="open">
                        <span class="khezana-filter-section__label">{{ __('items.fields.price') }}</span>
                        <svg class="khezana-filter-section__chevron" :class="{ 'is-open': open }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="khezana-filter-section__body" x-show="open" x-transition.duration.200ms>
                        <div class="khezana-price-slider"
                            :class="{ 'is-dragging-min': isDraggingMin, 'is-dragging-max': isDraggingMax }">
                            <div class="khezana-price-slider__labels">
                                <span x-text="formatPrice(minValue)">0</span>
                                <span x-text="formatPrice(maxValue)">1,000,000</span>
                            </div>
                            <div class="khezana-price-slider__track-wrap">
                                <div class="khezana-price-slider__track" @click="onTrackClick($event)">
                                    <div class="khezana-price-slider__range"
                                        :style="'left:' + minPercent + '%;width:' + (maxPercent - minPercent) + '%'">
                                    </div>
                                </div>
                                <input type="range" :min="min" :max="max" :step="step"
                                    :value="minValue" @input="updateMin($event.target.value)"
                                    @mousedown="onDragStart('min')" @mouseup="onDragEnd('min')"
                                    @touchstart="onDragStart('min')" @touchend="onDragEnd('min')"
                                    class="khezana-price-slider__input khezana-price-slider__input--min"
                                    aria-label="{{ __('common.ui.min_price') }}">
                                <input type="range" :min="min" :max="max" :step="step"
                                    :value="maxValue" @input="updateMax($event.target.value)"
                                    @mousedown="onDragStart('max')" @mouseup="onDragEnd('max')"
                                    @touchstart="onDragStart('max')" @touchend="onDragEnd('max')"
                                    class="khezana-price-slider__input khezana-price-slider__input--max"
                                    aria-label="{{ __('common.ui.max_price') }}">
                            </div>
                        </div>
                        <input type="hidden" name="price_min" :value="minValue">
                        <input type="hidden" name="price_max" :value="maxValue">
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
