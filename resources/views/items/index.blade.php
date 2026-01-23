@extends('layouts.app')

@section('title', __('common.ui.my_items_page') . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-listing-page">
        <div class="khezana-container">
            <!-- Page Header -->
            <div class="khezana-page-header">
                <div
                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--khezana-spacing-md);">
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

            <!-- Main Content -->
            <main class="khezana-listing-main">

                @if ($items->count() > 0)
                    <!-- Items Grid -->
                    <div class="khezana-items-grid-modern" role="list">
                        @foreach ($items as $item)
                            <div role="listitem">
                                @include('partials.item-card', ['item' => $item])
                            </div>
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
                            <a href="{{ route('items.create') }}" class="khezana-btn khezana-btn-primary khezana-btn-large">
                                {{ __('common.ui.no_items_cta') }}
                            </a>
                        </div>
                    </div>
                @endif
            </main>
        </div>
    </div>

@endsection
