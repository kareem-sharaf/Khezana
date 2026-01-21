@extends('layouts.app')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', __('requests.title') . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-listing-page">
        <div class="khezana-container">
            <!-- Page Header -->
            <div class="khezana-page-header">
                <div
                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--khezana-spacing-md);">
                    <div>
                        <h1 class="khezana-page-title">
                            {{ __('requests.title') }}
                        </h1>
                        <p class="khezana-page-subtitle">
                            {{ $requests->total() }} {{ __('requests.plural') }}
                        </p>
                    </div>
                    @auth
                        <a href="{{ route('requests.index') }}" class="khezana-btn khezana-btn-secondary">
                            {{ __('common.ui.my_requests') }}
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
                    <form method="GET" action="{{ route('public.requests.index') }}" class="khezana-filters-form" id="filtersForm">
                        <!-- Search Group -->
                        <div class="khezana-filter-section khezana-filter-section-expanded">
                            <button type="button" class="khezana-filter-section-header" aria-expanded="true">
                                <h3 class="khezana-filter-section-title">{{ __('common.ui.search') }}</h3>
                                <span class="khezana-filter-section-toggle">▼</span>
                            </button>
                            <div class="khezana-filter-section-content">
                                <div class="khezana-filter-group">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        placeholder="{{ __('common.ui.search_requests') }}" 
                                        class="khezana-filter-input khezana-filter-input-search">
                                </div>
                            </div>
                        </div>

                        <!-- Status Group -->
                        <div class="khezana-filter-section khezana-filter-section-expanded">
                            <button type="button" class="khezana-filter-section-header" aria-expanded="true">
                                <h3 class="khezana-filter-section-title">{{ __('requests.fields.status') }}</h3>
                                <span class="khezana-filter-section-toggle">▼</span>
                            </button>
                            <div class="khezana-filter-section-content">
                                <div class="khezana-filter-group">
                                    <select name="status" class="khezana-filter-select khezana-auto-submit">
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

                        <!-- Category Group -->
                        @if (isset($categories) && $categories->count() > 0)
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
                                        <option value="created_at_asc" {{ request('sort') == 'created_at_asc' ? 'selected' : '' }}>
                                            {{ __('common.ui.oldest') }}
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
                            @if (request()->hasAny(['search', 'status', 'category_id']))
                                <a href="{{ route('public.requests.index') }}" class="khezana-btn khezana-btn-secondary khezana-btn-reset-filters">
                                    {{ __('common.ui.reset') }}
                                </a>
                            @endif
                        </div>
                    </form>
                </aside>

                <!-- Main Content -->
                <main class="khezana-listing-main">
                    <!-- Active Filters Tags -->
                    @if (request()->hasAny(['search', 'status', 'category_id']))
                        <div class="khezana-active-filters">
                            @if (request('search'))
                                <span class="khezana-filter-tag">
                                    {{ __('common.ui.search') }}: {{ request('search') }}
                                    <a href="{{ route('public.requests.index', array_merge(request()->except('search'), ['page' => 1])) }}" class="khezana-filter-tag-remove">×</a>
                                </span>
                            @endif
                            @if (request('status'))
                                <span class="khezana-filter-tag">
                                    {{ __('requests.status.' . request('status')) }}
                                    <a href="{{ route('public.requests.index', array_merge(request()->except('status'), ['page' => 1])) }}" class="khezana-filter-tag-remove">×</a>
                                </span>
                            @endif
                            @if (request('category_id') && isset($categories) && $categories->firstWhere('id', request('category_id')))
                                <span class="khezana-filter-tag">
                                    {{ $categories->firstWhere('id', request('category_id'))->name }}
                                    <a href="{{ route('public.requests.index', array_merge(request()->except('category_id'), ['page' => 1])) }}" class="khezana-filter-tag-remove">×</a>
                                </span>
                            @endif
                            <a href="{{ route('public.requests.index') }}" class="khezana-filter-clear-all">{{ __('common.ui.clear_all') }}</a>
                        </div>
                    @endif

                    @if ($requests->count() > 0)
                        <!-- Requests Grid -->
                        <div class="khezana-requests-grid">
                            @foreach ($requests as $request)
                                <a href="{{ $request->url }}" class="khezana-request-card">
                                    <div class="khezana-request-content">
                                        <div class="khezana-request-header">
                                            <h3 class="khezana-request-title">{{ $request->title }}</h3>
                                            <span
                                                class="khezana-request-badge khezana-request-badge-{{ $request->status }}">
                                                {{ $request->statusLabel }}
                                            </span>
                                        </div>

                                        @if ($request->category)
                                            <p class="khezana-request-category">{{ $request->category->name }}</p>
                                        @endif

                                        @if ($request->description)
                                            <p class="khezana-request-description">
                                                {{ Str::limit($request->description, 120) }}
                                            </p>
                                        @endif

                                        @if ($request->attributes->count() > 0)
                                            <div class="khezana-request-attributes">
                                                @foreach ($request->attributes->take(3) as $attr)
                                                    <span class="khezana-request-attribute">
                                                        <strong>{{ $attr->name }}:</strong> {{ $attr->value }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="khezana-request-footer">
                                            <div class="khezana-request-meta">
                                                @if ($request->user)
                                                    <span class="khezana-request-user">
                                                        {{ $request->user->name }}
                                                    </span>
                                                @endif
                                                <span class="khezana-request-date">
                                                    {{ __('common.ui.posted') }} {{ $request->createdAtFormatted }}
                                                </span>
                                            </div>
                                            @if ($request->offersCount > 0)
                                                <span class="khezana-request-offers">
                                                    {{ $request->offersCount }} {{ __('common.ui.offers') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if ($requests->hasPages())
                            <div class="khezana-pagination">
                                {{ $requests->appends(request()->query())->links() }}
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
                                @if (request()->hasAny(['search', 'status', 'category_id']))
                                    <a href="{{ route('public.requests.index') }}" class="khezana-btn khezana-btn-secondary">
                                        {{ __('common.ui.clear_filters') }}
                                    </a>
                                @endif
                                @auth
                                    <a href="{{ route('requests.create') }}" class="khezana-btn khezana-btn-primary">
                                        {{ __('common.ui.no_results_cta_request') }}
                                    </a>
                                @else
                                    <a href="{{ route('public.requests.create-info') }}" class="khezana-btn khezana-btn-primary">
                                        {{ __('common.ui.no_results_cta_request') }}
                                    </a>
                                @endauth
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
