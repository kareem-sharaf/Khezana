@extends('layouts.app')

@section('title', __('items.title') . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-listing-page">
        <div class="khezana-container">
            <!-- Page Header -->
            <div class="khezana-page-header">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--khezana-spacing-md);">
                    <div>
                        <h1 class="khezana-page-title">
                            @if (request('operation_type') == 'sell')
                                {{ __('items.operation_types.sell') }}
                            @elseif(request('operation_type') == 'rent')
                                {{ __('items.operation_types.rent') }}
                            @elseif(request('operation_type') == 'donate')
                                {{ __('items.operation_types.donate') }}
                            @else
                                {{ __('items.title') }}
                            @endif
                        </h1>
                        <p class="khezana-page-subtitle">
                            {{ $items->total() }} {{ __('items.plural') }}
                        </p>
                    </div>
                    @auth
                        <a href="{{ route('items.index') }}" class="khezana-btn khezana-btn-secondary">
                            {{ __('common.ui.my_items') }}
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Mobile Filters Toggle Button -->
            <button type="button" class="khezana-filters-toggle" id="filtersToggle" aria-label="إظهار/إخفاء الفلاتر">
                <span class="khezana-filters-toggle-icon">🔍</span>
                <span class="khezana-filters-toggle-text">{{ __('common.ui.filters') }}</span>
                <span class="khezana-filters-toggle-count" id="activeFiltersCount"></span>
            </button>

            <div class="khezana-listing-layout">
                <!-- Sidebar Filters -->
                <aside class="khezana-filters-sidebar" id="filtersSidebar">
                    <form method="GET" action="{{ route('public.items.index') }}" class="khezana-filters-form" id="filtersForm">
                        <!-- Preserve operation_type if exists -->
                        @if (request('operation_type'))
                            <input type="hidden" name="operation_type" value="{{ request('operation_type') }}">
                        @endif

                        <!-- Search Group -->
                        <div class="khezana-filter-section khezana-filter-section-expanded">
                            <button type="button" class="khezana-filter-section-header" aria-expanded="true">
                                <h3 class="khezana-filter-section-title">{{ __('common.ui.search') }}</h3>
                                <span class="khezana-filter-section-toggle">▼</span>
                            </button>
                            <div class="khezana-filter-section-content">
                                <div class="khezana-filter-group">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        placeholder="{{ __('common.ui.search_items') }}"
                                        class="khezana-filter-input khezana-filter-input-search">
                                </div>
                            </div>
                        </div>

                        <!-- Operation Type Group -->
                        <div class="khezana-filter-section khezana-filter-section-expanded">
                            <button type="button" class="khezana-filter-section-header" aria-expanded="true">
                                <h3 class="khezana-filter-section-title">{{ __('items.fields.operation_type') }}</h3>
                                <span class="khezana-filter-section-toggle">▼</span>
                            </button>
                            <div class="khezana-filter-section-content">
                                <div class="khezana-filter-group">
                                <div class="khezana-filter-options">
                                    <label class="khezana-filter-option">
                                        <input type="radio" name="operation_type" value="sell"
                                            {{ request('operation_type') == 'sell' ? 'checked' : '' }}
                                            class="khezana-auto-submit">
                                        <span>{{ __('items.operation_types.sell') }}</span>
                                    </label>
                                    <label class="khezana-filter-option">
                                        <input type="radio" name="operation_type" value="rent"
                                            {{ request('operation_type') == 'rent' ? 'checked' : '' }}
                                            class="khezana-auto-submit">
                                        <span>{{ __('items.operation_types.rent') }}</span>
                                    </label>
                                    <label class="khezana-filter-option">
                                        <input type="radio" name="operation_type" value="donate"
                                            {{ request('operation_type') == 'donate' ? 'checked' : '' }}
                                            class="khezana-auto-submit">
                                        <span>{{ __('items.operation_types.donate') }}</span>
                                    </label>
                                    <label class="khezana-filter-option">
                                        <input type="radio" name="operation_type" value=""
                                            {{ !request('operation_type') ? 'checked' : '' }} class="khezana-auto-submit">
                                        <span>{{ __('common.ui.all') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Category Group -->
                        @if ($categories->count() > 0)
                            <div class="khezana-filter-section">
                                <button type="button" class="khezana-filter-section-header" aria-expanded="false">
                                    <h3 class="khezana-filter-section-title">{{ __('items.fields.category') }}</h3>
                                    <span class="khezana-filter-section-toggle">▼</span>
                                </button>
                                <div class="khezana-filter-section-content" style="display: none;">
                                    <div class="khezana-filter-group">
                                        <select name="category_id" class="khezana-filter-select khezana-auto-submit">
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

                        <!-- Price Range Group (only for sell/rent) -->
                        @if (in_array(request('operation_type'), ['sell', 'rent']) || !request('operation_type'))
                            <div class="khezana-filter-section">
                                <button type="button" class="khezana-filter-section-header" aria-expanded="false">
                                    <h3 class="khezana-filter-section-title">{{ __('common.ui.price_range') }}</h3>
                                    <span class="khezana-filter-section-toggle">▼</span>
                                </button>
                                <div class="khezana-filter-section-content" style="display: none;">
                                    <div class="khezana-filter-group">
                                        <div class="khezana-price-range">
                                            <input type="number" name="price_min" value="{{ request('price_min') }}"
                                                placeholder="{{ __('common.ui.min') }}"
                                                class="khezana-filter-input" min="0" step="0.01">
                                            <span class="khezana-price-separator">-</span>
                                            <input type="number" name="price_max" value="{{ request('price_max') }}"
                                                placeholder="{{ __('common.ui.max') }}"
                                                class="khezana-filter-input" min="0" step="0.01">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Sort Group -->
                        <div class="khezana-filter-section">
                            <button type="button" class="khezana-filter-section-header" aria-expanded="false">
                                <h3 class="khezana-filter-section-title">{{ __('common.ui.sort_by') }}</h3>
                                <span class="khezana-filter-section-toggle">▼</span>
                            </button>
                            <div class="khezana-filter-section-content" style="display: none;">
                                <div class="khezana-filter-group">
                                    <select name="sort" class="khezana-filter-select khezana-auto-submit">
                                        <option value="created_at_desc"
                                            {{ request('sort') == 'created_at_desc' ? 'selected' : '' }}>
                                            {{ __('common.ui.latest') }}
                                        </option>
                                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                                            {{ __('common.ui.price_low_to_high') }}
                                        </option>
                                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                                            {{ __('common.ui.price_high_to_low') }}
                                        </option>
                                        <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>
                                            {{ __('common.ui.title_a_z') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Action Buttons -->
                        <div class="khezana-filter-mobile-actions">
                            <button type="submit" class="khezana-btn khezana-btn-primary khezana-btn-apply-filters">
                                {{ __('common.ui.apply_filters') }}
                            </button>
                            @if (request()->hasAny(['search', 'operation_type', 'category_id', 'price_min', 'price_max']))
                                <a href="{{ route('public.items.index') }}" class="khezana-btn khezana-btn-secondary khezana-btn-reset-filters">
                                    {{ __('common.ui.reset') }}
                                </a>
                            @endif
                        </div>
                    </form>
                </aside>

                <!-- Main Content -->
                <main class="khezana-listing-main">
                    <!-- Active Filters Tags -->
                    @if (request()->hasAny(['search', 'operation_type', 'category_id', 'price_min', 'price_max']))
                        <div class="khezana-active-filters">
                            @if (request('search'))
                                <span class="khezana-filter-tag">
                                    {{ __('common.ui.search') }}: {{ request('search') }}
                                    <a href="{{ route('public.items.index', array_merge(request()->except('search'), ['page' => 1])) }}" class="khezana-filter-tag-remove">×</a>
                                </span>
                            @endif
                            @if (request('operation_type'))
                                <span class="khezana-filter-tag">
                                    {{ __('items.operation_types.' . request('operation_type')) }}
                                    <a href="{{ route('public.items.index', array_merge(request()->except('operation_type'), ['page' => 1])) }}" class="khezana-filter-tag-remove">×</a>
                                </span>
                            @endif
                            @if (request('category_id') && $categories->firstWhere('id', request('category_id')))
                                <span class="khezana-filter-tag">
                                    {{ $categories->firstWhere('id', request('category_id'))->name }}
                                    <a href="{{ route('public.items.index', array_merge(request()->except('category_id'), ['page' => 1])) }}" class="khezana-filter-tag-remove">×</a>
                                </span>
                            @endif
                            @if (request('price_min') || request('price_max'))
                                <span class="khezana-filter-tag">
                                    {{ __('common.ui.price') }}: 
                                    {{ request('price_min') ? number_format(request('price_min'), 0) : '0' }} - 
                                    {{ request('price_max') ? number_format(request('price_max'), 0) : '∞' }}
                                    <a href="{{ route('public.items.index', array_merge(request()->except(['price_min', 'price_max']), ['page' => 1])) }}" class="khezana-filter-tag-remove">×</a>
                                </span>
                            @endif
                            <a href="{{ route('public.items.index') }}" class="khezana-filter-clear-all">{{ __('common.ui.clear_all') }}</a>
                        </div>
                    @endif

                    @if ($items->count() > 0)
                        <!-- Items Grid -->
                        <div class="khezana-items-grid">
                            @foreach ($items as $item)
                                <a href="{{ route('public.items.show', ['id' => $item->id, 'slug' => $item->slug]) }}"
                                    class="khezana-item-card">
                                    @if ($item->primaryImage)
                                        <img src="{{ asset('storage/' . $item->primaryImage->path) }}"
                                            alt="{{ $item->title }}" class="khezana-item-image" loading="lazy">
                                    @else
                                        <div class="khezana-item-image"
                                            style="display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                                            {{ __('common.ui.no_image') }}
                                        </div>
                                    @endif
                                    <div class="khezana-item-content">
                                        <h3 class="khezana-item-title">{{ $item->title }}</h3>
                                        @if ($item->category)
                                            <p class="khezana-item-category">{{ $item->category->name }}</p>
                                        @endif
                                        @if ($item->createdAt)
                                            <p class="khezana-item-date">{{ __('common.ui.posted') }} {{ $item->createdAtFormatted }}</p>
                                        @endif
                                        <div class="khezana-item-footer">
                                            @if ($item->price && $item->operationType != 'donate')
                                                <div class="khezana-item-price">
                                                    {{ number_format($item->price, 0) }} {{ __('common.ui.currency') }}
                                                    @if ($item->operationType == 'rent')
                                                        <span class="khezana-price-unit">{{ __('common.ui.per_day') }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                            <span class="khezana-item-badge khezana-item-badge-{{ $item->operationType }}">
                                                {{ __('items.operation_types.' . $item->operationType) }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if ($items->hasPages())
                            <div class="khezana-pagination">
                                {{ $items->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <!-- Empty State -->
                        <div class="khezana-empty-state">
                            <div class="khezana-empty-icon">🔍</div>
                            <h3 class="khezana-empty-title">{{ __('common.messages.not_found') }}</h3>
                            <p class="khezana-empty-text">
                                {{ __('common.ui.no_results_message') }}
                            </p>
                            <div class="khezana-empty-actions">
                                @if (request()->hasAny(['search', 'operation_type', 'category_id', 'price_min', 'price_max']))
                                    <a href="{{ route('public.items.index') }}" class="khezana-btn khezana-btn-secondary">
                                        {{ __('common.ui.clear_filters') }}
                                    </a>
                                @endif
                                <a href="{{ route('public.requests.create-info') }}" class="khezana-btn khezana-btn-primary">
                                    {{ __('common.ui.no_results_cta_request') }}
                                </a>
                            </div>
                        </div>
                    @endif
                </main>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            try {
                const form = document.getElementById('filtersForm');
                const filtersToggle = document.getElementById('filtersToggle');
                const filtersSidebar = document.getElementById('filtersSidebar');
                const activeFiltersCount = document.getElementById('activeFiltersCount');
                
                if (!form) return;

                const isMobile = window.innerWidth <= 768;
                const autoSubmitElements = form.querySelectorAll('.khezana-auto-submit');

                // Mobile filters toggle
                if (filtersToggle && filtersSidebar && isMobile) {
                    filtersSidebar.style.display = 'none';
                    
                    filtersToggle.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        const isVisible = filtersSidebar.style.display !== 'none';
                        filtersSidebar.style.display = isVisible ? 'none' : 'block';
                        filtersToggle.setAttribute('aria-expanded', !isVisible);
                        
                        if (!isVisible) {
                            setTimeout(function() {
                                filtersSidebar.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                            }, 100);
                        }
                    });
                }

                // Update active filters count
                function updateFiltersCount() {
                    if (!activeFiltersCount) return;
                    const activeFilters = document.querySelectorAll('.khezana-filter-tag');
                    const count = activeFilters.length;
                    if (count > 0) {
                        activeFiltersCount.textContent = count;
                        activeFiltersCount.style.display = 'inline-flex';
                    } else {
                        activeFiltersCount.style.display = 'none';
                    }
                }
                updateFiltersCount();

                // Accordion for filter sections on mobile
                if (isMobile) {
                    const sectionHeaders = form.querySelectorAll('.khezana-filter-section-header');
                    sectionHeaders.forEach(function(header) {
                        header.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            const section = this.closest('.khezana-filter-section');
                            if (!section) return;
                            
                            const content = section.querySelector('.khezana-filter-section-content');
                            if (!content) return;
                            
                            const isExpanded = section.classList.contains('khezana-filter-section-expanded');
                            
                            if (isExpanded) {
                                section.classList.remove('khezana-filter-section-expanded');
                                content.style.display = 'none';
                                this.setAttribute('aria-expanded', 'false');
                            } else {
                                section.classList.add('khezana-filter-section-expanded');
                                content.style.display = 'block';
                                this.setAttribute('aria-expanded', 'true');
                            }
                        });
                    });
                }

                // Disable auto-submit on mobile
                if (isMobile) {
                    autoSubmitElements.forEach(function(el) {
                        if (el.hasAttribute('onchange')) {
                            el.removeAttribute('onchange');
                        }
                        el.classList.remove('khezana-auto-submit');
                    });
                } else {
                    // Enable auto-submit on desktop
                    autoSubmitElements.forEach(function(el) {
                        if (el.tagName === 'SELECT') {
                            el.addEventListener('change', function() {
                                if (form) form.submit();
                            });
                        } else if (el.type === 'radio') {
                            el.addEventListener('change', function() {
                                if (form) form.submit();
                            });
                        }
                    });
                }
            } catch (error) {
                console.error('Filters script error:', error);
            }
        });
    </script>
    @endpush
@endsection
