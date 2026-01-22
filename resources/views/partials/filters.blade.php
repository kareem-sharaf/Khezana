@php
    // Default values
    $type = $type ?? 'items'; // 'items' or 'requests'
    $route = $route ?? 'public.items.index';
    $resetRoute = $resetRoute ?? $route;
    $showOperationType = $showOperationType ?? $type === 'items';
    $showPriceRange = $showPriceRange ?? $type === 'items';
    $showStatus = $showStatus ?? $type === 'requests';
    $showApprovalStatus = $showApprovalStatus ?? false;
    $categories = $categories ?? collect();
    $preserveOperationType = $preserveOperationType ?? false;

    // Determine which filters are active for reset button
    $activeFilters = [];
    if ($showOperationType) {
        $activeFilters[] = 'operation_type';
    }
    if ($showPriceRange) {
        $activeFilters[] = 'price_min';
        $activeFilters[] = 'price_max';
    }
    if ($showStatus) {
        $activeFilters[] = 'status';
    }
    $activeFilters = array_merge(['search', 'category_id', 'condition'], $activeFilters);
    if ($showApprovalStatus) {
        $activeFilters[] = 'approval_status';
    }

    // Smart Accordion: Open sections that have active filters
    $hasSearch = request('search');
    $hasOperationType = request('operation_type');
    $hasCategory = request('category_id');
    $hasGovernorate = request('governorate');
    $hasCondition = request('condition');
    $hasPriceRange = request('price_min') || request('price_max');
    $hasStatus = request('status');
    $hasApprovalStatus = request('approval_status');
    $hasSort = request('sort') && request('sort') !== 'created_at_desc';
@endphp

<!-- Active Filters Bar (Desktop & Mobile) -->
<div class="khezana-active-filters-bar" id="activeFiltersBar">
    <div class="khezana-active-filters-content">
        <div class="khezana-active-filters-tags">
            @if (request('search'))
                <span class="khezana-filter-chip" data-filter="search">
                    <span class="khezana-filter-chip-label">{{ __('common.ui.search') }}: {{ request('search') }}</span>
                    <button type="button" class="khezana-filter-chip-remove" data-filter="search"
                        aria-label="إزالة">×</button>
                </span>
            @endif
            @if (request('operation_type'))
                <span class="khezana-filter-chip" data-filter="operation_type">
                    <span
                        class="khezana-filter-chip-label">{{ __('items.operation_types.' . request('operation_type')) }}</span>
                    <button type="button" class="khezana-filter-chip-remove" data-filter="operation_type"
                        aria-label="إزالة">×</button>
                </span>
            @endif
            @if (request('category_id') && $categories->firstWhere('id', request('category_id')))
                <span class="khezana-filter-chip" data-filter="category_id">
                    <span
                        class="khezana-filter-chip-label">{{ $categories->firstWhere('id', request('category_id'))->name }}</span>
                    <button type="button" class="khezana-filter-chip-remove" data-filter="category_id"
                        aria-label="إزالة">×</button>
                </span>
            @endif
            @if (request('governorate'))
                <span class="khezana-filter-chip" data-filter="governorate">
                    <span
                        class="khezana-filter-chip-label">{{ __('items.governorates.' . request('governorate')) }}</span>
                    <button type="button" class="khezana-filter-chip-remove" data-filter="governorate"
                        aria-label="إزالة">×</button>
                </span>
            @endif
            @if (request('condition'))
                <span class="khezana-filter-chip" data-filter="condition">
                    <span class="khezana-filter-chip-label">{{ __('items.conditions.' . request('condition')) }}</span>
                    <button type="button" class="khezana-filter-chip-remove" data-filter="condition"
                        aria-label="إزالة">×</button>
                </span>
            @endif
            @if ($hasPriceRange)
                <span class="khezana-filter-chip" data-filter="price">
                    <span class="khezana-filter-chip-label">
                        {{ __('common.ui.price') }}:
                        {{ request('price_min') ? number_format(request('price_min'), 0) : '0' }} -
                        {{ request('price_max') ? number_format(request('price_max'), 0) : '100,000' }}
                    </span>
                    <button type="button" class="khezana-filter-chip-remove" data-filter="price"
                        aria-label="إزالة">×</button>
                </span>
            @endif
            @if (request('status'))
                <span class="khezana-filter-chip" data-filter="status">
                    <span class="khezana-filter-chip-label">{{ __('requests.status.' . request('status')) }}</span>
                    <button type="button" class="khezana-filter-chip-remove" data-filter="status"
                        aria-label="إزالة">×</button>
                </span>
            @endif
            @if (request('approval_status'))
                <span class="khezana-filter-chip" data-filter="approval_status">
                    <span
                        class="khezana-filter-chip-label">{{ __('approvals.status.' . request('approval_status')) }}</span>
                    <button type="button" class="khezana-filter-chip-remove" data-filter="approval_status"
                        aria-label="إزالة">×</button>
                </span>
            @endif
        </div>
        @if (request()->hasAny($activeFilters))
            <button type="button" class="khezana-filter-clear-all-btn" id="clearAllFilters">
                {{ __('common.ui.clear_all') }}
            </button>
        @endif
    </div>
</div>

<!-- Quick Filters (Mobile Only) -->
<div class="khezana-quick-filters" id="quickFilters">
    <div class="khezana-quick-filters-scroll">
        @if ($showOperationType)
            <button type="button" class="khezana-quick-filter-chip" data-filter="operation_type" data-value="sell">
                {{ __('items.operation_types.sell') }}
            </button>
            <button type="button" class="khezana-quick-filter-chip" data-filter="operation_type" data-value="rent">
                {{ __('items.operation_types.rent') }}
            </button>
            <button type="button" class="khezana-quick-filter-chip" data-filter="operation_type" data-value="donate">
                {{ __('items.operation_types.donate') }}
            </button>
        @endif
        <button type="button" class="khezana-quick-filter-chip" data-filter="condition" data-value="new">
            {{ __('items.conditions.new') }}
        </button>
        <button type="button" class="khezana-quick-filter-chip" data-filter="condition" data-value="used">
            {{ __('items.conditions.used') }}
        </button>
    </div>
</div>

<!-- Filters Sidebar (Desktop) / Bottom Sheet (Mobile) -->
<aside class="khezana-filters-sidebar" id="filtersSidebar">
    <!-- Mobile Bottom Sheet Header -->
    <div class="khezana-filters-sheet-header">
        <h3 class="khezana-filters-sheet-title">{{ __('common.ui.filters') }}</h3>
        <button type="button" class="khezana-filters-sheet-close" id="closeFiltersSheet" aria-label="إغلاق">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <form method="GET" action="{{ route($route) }}" class="khezana-filters-form" id="filtersForm">
        @if ($preserveOperationType && request('operation_type'))
            <input type="hidden" name="operation_type" value="{{ request('operation_type') }}">
        @endif

        <!-- PRIMARY FILTERS (Always Visible) -->
        <div class="khezana-filters-primary">
            <!-- Search - Primary -->
            <div class="khezana-filter-group-primary">
                <label class="khezana-filter-label-primary">{{ __('common.ui.search') }}</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="{{ $type === 'items' ? __('common.ui.search_items') : __('common.ui.search_requests') }}"
                    class="khezana-filter-input khezana-filter-input-search" id="filterSearch">
            </div>

            @if ($showOperationType)
                <!-- Operation Type - Primary -->
                <div class="khezana-filter-group-primary">
                    <label class="khezana-filter-label-primary">{{ __('items.fields.operation_type') }}</label>
                    <div class="khezana-filter-options-inline">
                        <label class="khezana-filter-option-inline">
                            <input type="radio" name="operation_type" value="sell"
                                {{ request('operation_type') == 'sell' ? 'checked' : '' }}>
                            <span>{{ __('items.operation_types.sell') }}</span>
                        </label>
                        <label class="khezana-filter-option-inline">
                            <input type="radio" name="operation_type" value="rent"
                                {{ request('operation_type') == 'rent' ? 'checked' : '' }}>
                            <span>{{ __('items.operation_types.rent') }}</span>
                        </label>
                        <label class="khezana-filter-option-inline">
                            <input type="radio" name="operation_type" value="donate"
                                {{ request('operation_type') == 'donate' ? 'checked' : '' }}>
                            <span>{{ __('items.operation_types.donate') }}</span>
                        </label>
                        <label class="khezana-filter-option-inline">
                            <input type="radio" name="operation_type" value=""
                                {{ !request('operation_type') ? 'checked' : '' }}>
                            <span>{{ __('common.ui.all') }}</span>
                        </label>
                    </div>
                </div>
            @endif
        </div>

        <!-- SECONDARY FILTERS (Accordion - Smart Open) -->
        <div class="khezana-filters-secondary">
            <!-- Category - Secondary -->
            @if ($categories->count() > 0)
                <div class="khezana-filter-section khezana-filter-section-secondary">
                    <input type="checkbox" id="filter-category" class="khezana-filter-checkbox"
                        {{ $hasCategory ? 'checked' : '' }}>
                    <label for="filter-category" class="khezana-filter-section-header">
                        <h3 class="khezana-filter-section-title">{{ __('items.fields.category') }}</h3>
                        <span class="khezana-filter-section-toggle"></span>
                    </label>
                    <div class="khezana-filter-section-content">
                        <div class="khezana-filter-group">
                            <select name="category_id" class="khezana-filter-select" id="filterCategory">
                                <option value="">{{ __('common.ui.all_categories') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @if ($category->children->count() > 0)
                                        @foreach ($category->children as $child)
                                            <option value="{{ $child->id }}"
                                                {{ request('category_id') == $child->id ? 'selected' : '' }}>
                                                &nbsp;&nbsp;{{ $child->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Governorate - Secondary -->
            <div class="khezana-filter-section khezana-filter-section-secondary">
                <input type="checkbox" id="filter-governorate" class="khezana-filter-checkbox"
                    {{ $hasGovernorate ? 'checked' : '' }}>
                <label for="filter-governorate" class="khezana-filter-section-header">
                    <h3 class="khezana-filter-section-title">📍 {{ __('items.fields.governorate') }}</h3>
                    <span class="khezana-filter-section-toggle"></span>
                </label>
                <div class="khezana-filter-section-content">
                    <div class="khezana-filter-group">
                        <select name="governorate" class="khezana-filter-select" id="filterGovernorate">
                            <option value="">{{ __('common.ui.all_governorates') }}</option>
                            @foreach (['damascus' => __('items.governorates.damascus'), 'aleppo' => __('items.governorates.aleppo'), 'homs' => __('items.governorates.homs'), 'hama' => __('items.governorates.hama'), 'latakia' => __('items.governorates.latakia'), 'tartus' => __('items.governorates.tartus'), 'daraa' => __('items.governorates.daraa'), 'sweida' => __('items.governorates.sweida'), 'hasakah' => __('items.governorates.hasakah'), 'deir_ezzor' => __('items.governorates.deir_ezzor'), 'raqqa' => __('items.governorates.raqqa'), 'idlib' => __('items.governorates.idlib')] as $key => $name)
                                <option value="{{ $key }}"
                                    {{ request('governorate') === $key ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Condition - Secondary -->
            <div class="khezana-filter-section khezana-filter-section-secondary">
                <input type="checkbox" id="filter-condition" class="khezana-filter-checkbox"
                    {{ $hasCondition ? 'checked' : '' }}>
                <label for="filter-condition" class="khezana-filter-section-header">
                    <h3 class="khezana-filter-section-title">🏷️ {{ __('items.fields.condition') }}</h3>
                    <span class="khezana-filter-section-toggle"></span>
                </label>
                <div class="khezana-filter-section-content">
                    <div class="khezana-filter-group">
                        <div class="khezana-filter-options">
                            <label class="khezana-filter-option">
                                <input type="radio" name="condition" value=""
                                    {{ !request('condition') ? 'checked' : '' }}>
                                <span>{{ __('common.ui.all') }}</span>
                            </label>
                            <label class="khezana-filter-option">
                                <input type="radio" name="condition" value="new"
                                    {{ request('condition') == 'new' ? 'checked' : '' }}>
                                <span>{{ __('items.conditions.new') }}</span>
                            </label>
                            <label class="khezana-filter-option">
                                <input type="radio" name="condition" value="used"
                                    {{ request('condition') == 'used' ? 'checked' : '' }}>
                                <span>{{ __('items.conditions.used') }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            @if ($showStatus)
                <!-- Status - Secondary -->
                <div class="khezana-filter-section khezana-filter-section-secondary">
                    <input type="checkbox" id="filter-status" class="khezana-filter-checkbox"
                        {{ $hasStatus ? 'checked' : '' }}>
                    <label for="filter-status" class="khezana-filter-section-header">
                        <h3 class="khezana-filter-section-title">{{ __('requests.fields.status') }}</h3>
                        <span class="khezana-filter-section-toggle"></span>
                    </label>
                    <div class="khezana-filter-section-content">
                        <div class="khezana-filter-group">
                            <select name="status" class="khezana-filter-select" id="filterStatus">
                                <option value="">{{ __('common.ui.all') }}</option>
                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>
                                    {{ __('requests.status.open') }}
                                </option>
                                <option value="fulfilled" {{ request('status') == 'fulfilled' ? 'selected' : '' }}>
                                    {{ __('requests.status.fulfilled') }}
                                </option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>
                                    {{ __('requests.status.closed') }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- ADVANCED FILTERS (Accordion - Closed by Default) -->
        <div class="khezana-filters-advanced">
            <div class="khezana-filters-advanced-header">
                <button type="button" class="khezana-filters-advanced-toggle" id="toggleAdvancedFilters">
                    <span>{{ __('common.ui.advanced_filters') }}</span>
                    <svg class="khezana-chevron-icon" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
            </div>
            <div class="khezana-filters-advanced-content" id="advancedFiltersContent">
                @if ($showPriceRange && (in_array(request('operation_type'), ['sell', 'rent']) || !request('operation_type')))
                    <!-- Price Range - Advanced -->
                    <div class="khezana-filter-section khezana-filter-section-advanced">
                        <input type="checkbox" id="filter-price-range" class="khezana-filter-checkbox"
                            {{ $hasPriceRange ? 'checked' : '' }}>
                        <label for="filter-price-range" class="khezana-filter-section-header">
                            <h3 class="khezana-filter-section-title">{{ __('common.ui.price_range') }}</h3>
                            <span class="khezana-filter-section-toggle"></span>
                        </label>
                        <div class="khezana-filter-section-content">
                            <div class="khezana-filter-group">
                                <div class="khezana-price-range-simplified">
                                    <div class="khezana-price-inputs">
                                        <div class="khezana-price-input-group">
                                            <label class="khezana-price-input-label">{{ __('common.ui.min') }}</label>
                                            <input type="number" name="price_min" id="priceMinInput"
                                                value="{{ request('price_min') ?: 0 }}" min="0"
                                                max="100000" step="1000" class="khezana-price-input"
                                                placeholder="0">
                                        </div>
                                        <span class="khezana-price-separator">-</span>
                                        <div class="khezana-price-input-group">
                                            <label class="khezana-price-input-label">{{ __('common.ui.max') }}</label>
                                            <input type="number" name="price_max" id="priceMaxInput"
                                                value="{{ request('price_max') ?: 100000 }}" min="0"
                                                max="100000" step="1000" class="khezana-price-input"
                                                placeholder="100,000">
                                        </div>
                                    </div>
                                    <div class="khezana-price-slider-toggle">
                                        <button type="button" class="khezana-price-slider-btn"
                                            id="togglePriceSlider">
                                            {{ __('common.ui.use_slider') }}
                                        </button>
                                    </div>
                                    <div class="khezana-price-range-slider" id="priceSliderContainer"
                                        style="display: none;">
                                        <div class="khezana-slider-container">
                                            <div class="khezana-slider-labels">
                                                <span class="khezana-slider-label-start">0</span>
                                                <span class="khezana-slider-label-end">100,000</span>
                                            </div>
                                            <input type="range" id="priceMaxSlider"
                                                value="{{ request('price_max') ?: 100000 }}" min="0"
                                                max="100000" step="1000" class="khezana-price-slider">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($showApprovalStatus)
                    <!-- Approval Status - Advanced -->
                    <div class="khezana-filter-section khezana-filter-section-advanced">
                        <input type="checkbox" id="filter-approval-status" class="khezana-filter-checkbox"
                            {{ $hasApprovalStatus ? 'checked' : '' }}>
                        <label for="filter-approval-status" class="khezana-filter-section-header">
                            <h3 class="khezana-filter-section-title">{{ __('approvals.fields.status') }}</h3>
                            <span class="khezana-filter-section-toggle"></span>
                        </label>
                        <div class="khezana-filter-section-content">
                            <div class="khezana-filter-group">
                                <select name="approval_status" class="khezana-filter-select"
                                    id="filterApprovalStatus">
                                    <option value="">{{ __('common.ui.all') }}</option>
                                    <option value="pending"
                                        {{ request('approval_status') == 'pending' ? 'selected' : '' }}>
                                        {{ __('approvals.status.pending') }}
                                    </option>
                                    <option value="approved"
                                        {{ request('approval_status') == 'approved' ? 'selected' : '' }}>
                                        {{ __('approvals.status.approved') }}
                                    </option>
                                    <option value="rejected"
                                        {{ request('approval_status') == 'rejected' ? 'selected' : '' }}>
                                        {{ __('approvals.status.rejected') }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Sort - Advanced -->
                <div class="khezana-filter-section khezana-filter-section-advanced">
                    <input type="checkbox" id="filter-sort" class="khezana-filter-checkbox"
                        {{ $hasSort ? 'checked' : '' }}>
                    <label for="filter-sort" class="khezana-filter-section-header">
                        <h3 class="khezana-filter-section-title">{{ __('common.ui.sort_by') }}</h3>
                        <span class="khezana-filter-section-toggle"></span>
                    </label>
                    <div class="khezana-filter-section-content">
                        <div class="khezana-filter-group">
                            <select name="sort" class="khezana-filter-select" id="filterSort">
                                <option value="created_at_desc"
                                    {{ request('sort') == 'created_at_desc' ? 'selected' : '' }}>
                                    {{ __('common.ui.latest') }}
                                </option>
                                @if ($type === 'items')
                                    <option value="created_at_asc"
                                        {{ request('sort') == 'created_at_asc' ? 'selected' : '' }}>
                                        {{ __('common.ui.oldest') }}
                                    </option>
                                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                                        {{ __('common.ui.price_low_to_high') }}
                                    </option>
                                    <option value="price_desc"
                                        {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                                        {{ __('common.ui.price_high_to_low') }}
                                    </option>
                                    <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>
                                        {{ __('common.ui.title_a_z') }}
                                    </option>
                                @else
                                    <option value="created_at_asc"
                                        {{ request('sort') == 'created_at_asc' ? 'selected' : '' }}>
                                        {{ __('common.ui.oldest') }}
                                    </option>
                                    <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>
                                        {{ __('common.ui.title_a_z') }}
                                    </option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Apply Button (Sticky Bottom) -->
        <div class="khezana-filter-actions-mobile">
            <button type="submit" class="khezana-btn khezana-btn-primary khezana-btn-apply-filters-mobile"
                id="applyFiltersBtn">
                <span class="khezana-btn-apply-text">{{ __('common.ui.apply_filters') }}</span>
                <span class="khezana-btn-apply-count" id="activeFiltersCount" style="display: none;"></span>
            </button>
        </div>
    </form>
</aside>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filtersForm = document.getElementById('filtersForm');
            const activeFiltersBar = document.getElementById('activeFiltersBar');
            const applyFiltersBtn = document.getElementById('applyFiltersBtn');
            const activeFiltersCount = document.getElementById('activeFiltersCount');
            const priceMaxSlider = document.getElementById('priceMaxSlider');
            const priceMinInput = document.getElementById('priceMinInput');
            const priceMaxInput = document.getElementById('priceMaxInput');
            const togglePriceSlider = document.getElementById('togglePriceSlider');
            const priceSliderContainer = document.getElementById('priceSliderContainer');
            const toggleAdvancedFilters = document.getElementById('toggleAdvancedFilters');
            const advancedFiltersContent = document.getElementById('advancedFiltersContent');
            const closeFiltersSheet = document.getElementById('closeFiltersSheet');
            const mobileFiltersToggle = document.getElementById('mobileFiltersToggle');
            const filtersSidebar = document.getElementById('filtersSidebar');
            const quickFilters = document.getElementById('quickFilters');

            // Auto-Apply functionality
            let autoApplyTimeout;
            const autoApplyDelay = 500; // 500ms delay for search input, instant for others

            function autoApply() {
                clearTimeout(autoApplyTimeout);
                autoApplyTimeout = setTimeout(() => {
                    filtersForm.submit();
                }, autoApplyDelay);
            }

            // Instant auto-apply for selects and radios
            const instantAutoApplyElements = filtersForm.querySelectorAll('select, input[type="radio"]');
            instantAutoApplyElements.forEach(el => {
                el.addEventListener('change', () => {
                    clearTimeout(autoApplyTimeout);
                    filtersForm.submit();
                });
            });

            // Delayed auto-apply for search input
            const searchInput = document.getElementById('filterSearch');
            if (searchInput) {
                let searchTimeout;
                searchInput.addEventListener('input', () => {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        filtersForm.submit();
                    }, autoApplyDelay);
                });
            }

            // Price inputs auto-apply
            if (priceMinInput) {
                priceMinInput.addEventListener('change', autoApply);
            }
            if (priceMaxInput) {
                priceMaxInput.addEventListener('change', autoApply);
            }

            // Price slider sync with inputs
            if (priceMaxSlider && priceMaxInput) {
                priceMaxSlider.addEventListener('input', function() {
                    priceMaxInput.value = this.value;
                    autoApply();
                });

                priceMaxInput.addEventListener('input', function() {
                    if (this.value) {
                        priceMaxSlider.value = Math.min(parseInt(this.value), 100000);
                    }
                });
            }

            // Toggle price slider
            if (togglePriceSlider && priceSliderContainer) {
                togglePriceSlider.addEventListener('click', function() {
                    const isVisible = priceSliderContainer.style.display !== 'none';
                    priceSliderContainer.style.display = isVisible ? 'none' : 'block';
                    this.textContent = isVisible ? '{{ __('common.ui.use_slider') }}' :
                        '{{ __('common.ui.hide_slider') }}';
                });
            }

            // Toggle advanced filters
            if (toggleAdvancedFilters && advancedFiltersContent) {
                toggleAdvancedFilters.addEventListener('click', function() {
                    const isExpanded = advancedFiltersContent.classList.contains('expanded');
                    advancedFiltersContent.classList.toggle('expanded');
                    const chevron = this.querySelector('.khezana-chevron-icon');
                    if (chevron) {
                        chevron.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(180deg)';
                    }
                });
            }

            // Update active filters count
            function updateActiveFiltersCount() {
                const activeChips = activeFiltersBar.querySelectorAll('.khezana-filter-chip');
                const count = activeChips.length;
                if (count > 0) {
                    activeFiltersCount.textContent = `(${count})`;
                    activeFiltersCount.style.display = 'inline';
                    applyFiltersBtn.classList.add('has-filters');
                } else {
                    activeFiltersCount.style.display = 'none';
                    applyFiltersBtn.classList.remove('has-filters');
                }
            }

            // Remove filter chip
            activeFiltersBar.addEventListener('click', function(e) {
                if (e.target.classList.contains('khezana-filter-chip-remove') || e.target.closest(
                        '.khezana-filter-chip-remove')) {
                    e.preventDefault();
                    const chip = e.target.closest('.khezana-filter-chip');
                    const filterName = chip.dataset.filter;

                    if (filterName === 'price') {
                        if (priceMinInput) priceMinInput.value = '0';
                        if (priceMaxInput) priceMaxInput.value = '100000';
                        if (priceMaxSlider) priceMaxSlider.value = '100000';
                    } else if (filterName === 'search') {
                        if (searchInput) searchInput.value = '';
                    } else if (filterName === 'operation_type') {
                        const radio = filtersForm.querySelector('input[name="operation_type"][value=""]');
                        if (radio) radio.checked = true;
                    } else if (filterName === 'category_id') {
                        const select = document.getElementById('filterCategory');
                        if (select) select.value = '';
                    } else if (filterName === 'governorate') {
                        const select = document.getElementById('filterGovernorate');
                        if (select) select.value = '';
                    } else if (filterName === 'condition') {
                        const radio = filtersForm.querySelector('input[name="condition"][value=""]');
                        if (radio) radio.checked = true;
                    } else if (filterName === 'status') {
                        const select = document.getElementById('filterStatus');
                        if (select) select.value = '';
                    } else if (filterName === 'approval_status') {
                        const select = document.getElementById('filterApprovalStatus');
                        if (select) select.value = '';
                    }

                    filtersForm.submit();
                }
            });

            // Clear all filters
            const clearAllBtn = document.getElementById('clearAllFilters');
            if (clearAllBtn) {
                clearAllBtn.addEventListener('click', function() {
                    window.location.href = '{{ route($resetRoute) }}';
                });
            }

            // Mobile Bottom Sheet
            if (mobileFiltersToggle) {
                mobileFiltersToggle.addEventListener('change', function() {
                    if (this.checked) {
                        filtersSidebar.classList.add('open');
                        document.body.style.overflow = 'hidden';
                    } else {
                        filtersSidebar.classList.remove('open');
                        document.body.style.overflow = '';
                    }
                });
            }

            if (closeFiltersSheet) {
                closeFiltersSheet.addEventListener('click', function() {
                    if (mobileFiltersToggle) {
                        mobileFiltersToggle.checked = false;
                        filtersSidebar.classList.remove('open');
                        document.body.style.overflow = '';
                    }
                });
            }

            // Bottom sheet drag to close
            let startY = 0;
            let currentY = 0;
            let isDragging = false;

            if (filtersSidebar) {
                const sheetHeader = filtersSidebar.querySelector('.khezana-filters-sheet-header');
                if (sheetHeader) {
                    sheetHeader.addEventListener('touchstart', function(e) {
                        startY = e.touches[0].clientY;
                        isDragging = true;
                    });

                    sheetHeader.addEventListener('touchmove', function(e) {
                        if (!isDragging) return;
                        currentY = e.touches[0].clientY;
                        const diff = currentY - startY;
                        if (diff > 0) {
                            filtersSidebar.style.transform = `translateY(${diff}px)`;
                        }
                    });

                    sheetHeader.addEventListener('touchend', function() {
                        if (currentY - startY > 100) {
                            if (mobileFiltersToggle) {
                                mobileFiltersToggle.checked = false;
                                filtersSidebar.classList.remove('open');
                                document.body.style.overflow = '';
                            }
                        }
                        filtersSidebar.style.transform = '';
                        isDragging = false;
                    });
                }
            }

            // Quick filters (mobile)
            if (quickFilters) {
                quickFilters.addEventListener('click', function(e) {
                    const chip = e.target.closest('.khezana-quick-filter-chip');
                    if (chip) {
                        const filterName = chip.dataset.filter;
                        const filterValue = chip.dataset.value;

                        if (filterName === 'operation_type') {
                            const radio = filtersForm.querySelector(
                                `input[name="operation_type"][value="${filterValue}"]`);
                            if (radio) {
                                radio.checked = true;
                                filtersForm.submit();
                            }
                        } else if (filterName === 'condition') {
                            const radio = filtersForm.querySelector(
                                `input[name="condition"][value="${filterValue}"]`);
                            if (radio) {
                                radio.checked = true;
                                filtersForm.submit();
                            }
                        }
                    }
                });
            }

            // Update active filters count on load
            updateActiveFiltersCount();

            // Show/hide active filters bar
            const hasActiveFilters = activeFiltersBar.querySelectorAll('.khezana-filter-chip').length > 0;
            if (hasActiveFilters) {
                activeFiltersBar.classList.add('has-filters');
            }
        });
    </script>
@endpush
