@extends('layouts.app')

@section('title', __('common.ui.my_items_page') . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-listing-page">
        <div class="khezana-container">
            <!-- Page Header -->
            <div class="khezana-page-header">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--khezana-spacing-md);">
                    <div>
                        <h1 class="khezana-page-title">{{ __('common.ui.my_items_page') }}</h1>
                        <p class="khezana-page-subtitle">
                            {{ $items->total() }} {{ __('items.plural') }}
                        </p>
                    </div>
                    <a href="{{ route('items.create') }}" class="khezana-btn khezana-btn-primary">
                        {{ __('common.ui.add_new_item') }}
                    </a>
                </div>
            </div>

            <!-- Mobile Filters Toggle Button -->
            <input type="checkbox" id="mobileFiltersToggle" class="khezana-mobile-toggle-checkbox">
            <label for="mobileFiltersToggle" class="khezana-filters-toggle">
                <span class="khezana-filters-toggle-icon">🔍</span>
                <span class="khezana-filters-toggle-text">{{ __('common.ui.filters') }}</span>
            </label>

            <div class="khezana-listing-layout">
                @include('partials.filters', [
                    'type' => 'items',
                    'route' => 'items.index',
                    'resetRoute' => 'items.index',
                    'showOperationType' => true,
                    'showPriceRange' => false,
                    'showStatus' => false,
                    'showApprovalStatus' => true,
                    'categories' => $categories,
                ])

                <!-- Main Content -->
                <main class="khezana-listing-main">
                    <!-- Active Filters Tags -->
                    @if (request()->hasAny(['search', 'operation_type', 'category_id', 'approval_status']))
                        <div class="khezana-active-filters">
                            @if (request('search'))
                                <span class="khezana-filter-tag">
                                    {{ __('common.ui.search') }}: {{ request('search') }}
                                    <a href="{{ route('items.index', array_merge(request()->except('search'), ['page' => 1])) }}" class="khezana-filter-tag-remove">×</a>
                                </span>
                            @endif
                            @if (request('operation_type'))
                                <span class="khezana-filter-tag">
                                    {{ __('items.operation_types.' . request('operation_type')) }}
                                    <a href="{{ route('items.index', array_merge(request()->except('operation_type'), ['page' => 1])) }}" class="khezana-filter-tag-remove">×</a>
                                </span>
                            @endif
                            @if (request('category_id') && $categories->firstWhere('id', request('category_id')))
                                <span class="khezana-filter-tag">
                                    {{ $categories->firstWhere('id', request('category_id'))->name }}
                                    <a href="{{ route('items.index', array_merge(request()->except('category_id'), ['page' => 1])) }}" class="khezana-filter-tag-remove">×</a>
                                </span>
                            @endif
                            @if (request('approval_status'))
                                <span class="khezana-filter-tag">
                                    {{ __('approvals.status.' . request('approval_status')) }}
                                    <a href="{{ route('items.index', array_merge(request()->except('approval_status'), ['page' => 1])) }}" class="khezana-filter-tag-remove">×</a>
                                </span>
                            @endif
                            <a href="{{ route('items.index') }}" class="khezana-filter-clear-all">{{ __('common.ui.clear_all') }}</a>
                        </div>
                    @endif

                @if ($items->count() > 0)
                    <!-- Items Grid -->
                    <div class="khezana-items-grid">
                        @foreach ($items as $item)
                            <a href="{{ route('items.show', $item) }}" class="khezana-item-card">
                                @php
                                    $primaryImage = $item->images->where('is_primary', true)->first() ?? $item->images->first();
                                @endphp
                                @if ($primaryImage)
                                    <img src="{{ asset('storage/' . $primaryImage->path) }}"
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
                                    <div class="khezana-item-footer">
                                        @if ($item->price && $item->operation_type->value != 'donate')
                                            <div class="khezana-item-price">
                                                {{ number_format($item->price, 0) }} {{ __('common.ui.currency') }}
                                                @if ($item->operation_type->value == 'rent')
                                                    <span class="khezana-price-unit">{{ __('common.ui.per_day') }}</span>
                                                @endif
                                            </div>
                                        @endif
                                        <div style="display: flex; gap: var(--khezana-spacing-xs); flex-wrap: wrap;">
                                            <span class="khezana-item-badge khezana-item-badge-{{ $item->operation_type->value }}">
                                                {{ __('items.operation_types.' . $item->operation_type->value) }}
                                            </span>

                                            @if($item->approvalRelation)
                                                @php
                                                    $status = $item->approvalRelation->status;
                                                    $statusClass = match($status->value) {
                                                        'approved' => 'khezana-approval-badge-approved',
                                                        'pending' => 'khezana-approval-badge-pending',
                                                        'rejected' => 'khezana-approval-badge-rejected',
                                                        'archived' => 'khezana-approval-badge-archived',
                                                        default => 'khezana-approval-badge-pending',
                                                    };
                                                @endphp
                                                <span class="khezana-approval-badge {{ $statusClass }}" style="font-size: 0.75rem;">
                                                    {{ $status->label() }}
                                                </span>
                                            @endif
                                        </div>
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
                        <div class="khezana-empty-icon">📦</div>
                        <h3 class="khezana-empty-title">{{ __('common.ui.no_items') }}</h3>
                        <p class="khezana-empty-text">
                            {{ __('common.ui.no_items_message') }}
                        </p>
                        <div class="khezana-empty-actions">
                            @if (request()->hasAny(['search', 'operation_type', 'category_id', 'approval_status']))
                                <a href="{{ route('items.index') }}" class="khezana-btn khezana-btn-secondary">
                                    {{ __('common.ui.clear_filters') }}
                                </a>
                            @endif
                            <a href="{{ route('items.create') }}" class="khezana-btn khezana-btn-primary khezana-btn-large">
                                {{ __('common.ui.no_items_cta') }}
                            </a>
                        </div>
                    </div>
                @endif
                </main>
            </div>
        </div>
    </div>

@endsection
