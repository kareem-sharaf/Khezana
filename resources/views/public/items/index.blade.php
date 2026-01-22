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
            <input type="checkbox" id="mobileFiltersToggle" class="khezana-mobile-toggle-checkbox">
            <label for="mobileFiltersToggle" class="khezana-filters-toggle">
                <span class="khezana-filters-toggle-icon">🔍</span>
                <span class="khezana-filters-toggle-text">{{ __('common.ui.filters') }}</span>
            </label>

            <div class="khezana-listing-layout">
                @include('partials.filters', [
                    'type' => 'items',
                    'route' => 'public.items.index',
                    'resetRoute' => 'public.items.index',
                    'showOperationType' => true,
                    'showPriceRange' => true,
                    'showStatus' => false,
                    'showApprovalStatus' => false,
                    'categories' => $categories,
                    'preserveOperationType' => true,
                ])

                <!-- Main Content -->
                <main class="khezana-listing-main">

                    @if ($items->count() > 0)
                        <!-- Items Grid -->
                        <div class="khezana-items-grid">
                            @foreach ($items as $item)
                                <a href="{{ route('public.items.show', ['id' => $item->id, 'slug' => $item->slug]) }}"
                                    class="khezana-item-card" data-item-id="{{ $item->id }}">
                                    <div class="khezana-item-image-wrapper">
                                        @if ($item->primaryImage)
                                            <div class="khezana-item-image-container">
                                                <img src="{{ asset('storage/' . $item->primaryImage->path) }}"
                                                    alt="{{ $item->title }}"
                                                    class="khezana-item-image"
                                                    loading="lazy"
                                                    data-primary-image="{{ asset('storage/' . $item->primaryImage->path) }}">
                                                @if ($item->images->count() > 1)
                                                    <div class="khezana-item-image-overlay">
                                                        <span class="khezana-item-image-count">+{{ $item->images->count() - 1 }}</span>
                                                    </div>
                                                    <div class="khezana-item-image-preview" style="display: none;">
                                                        @foreach ($item->images->take(4) as $image)
                                                            <img src="{{ asset('storage/' . $image->path) }}"
                                                                alt="{{ $item->title }}"
                                                                class="khezana-preview-image"
                                                                data-image-index="{{ $loop->index }}"
                                                                loading="lazy">
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="khezana-item-image-placeholder">
                                                <svg class="khezana-placeholder-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                                    <polyline points="21 15 16 10 5 21"/>
                                                </svg>
                                                <span class="khezana-placeholder-text">{{ __('common.ui.no_image') }}</span>
                                            </div>
                                        @endif
                                        <div class="khezana-item-image-skeleton" style="display: none;">
                                            <div class="khezana-skeleton-shimmer"></div>
                                        </div>
                                    </div>
                                    <div class="khezana-item-content">
                                        <h3 class="khezana-item-title">{{ $item->title }}</h3>
                                        @if ($item->condition)
                                            <div class="khezana-item-meta-row">
                                                <span class="khezana-item-meta-badge">🏷️ {{ __('items.conditions.' . $item->condition) }}</span>
                                            </div>
                                        @endif
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

@endsection
