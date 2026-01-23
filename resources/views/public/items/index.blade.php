@extends('layouts.app')

@section('title', __('items.title') . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-listing-page">
        <div class="khezana-container">
            <!-- Page Header -->
            <header class="khezana-page-header">
                <div class="khezana-page-header-content">
                    <div class="khezana-page-header-text">
                        <h1 class="khezana-page-title">
                            @if (request('operation_type') == 'sell')
                                {{ __('items.operation_types.sell') }} - {{ __('items.title') }}
                            @elseif(request('operation_type') == 'rent')
                                {{ __('items.operation_types.rent') }} - {{ __('items.title') }}
                            @elseif(request('operation_type') == 'donate')
                                {{ __('items.operation_types.donate') }} - {{ __('items.title') }}
                            @else
                                {{ __('items.title') }}
                            @endif
                        </h1>
                        <p class="khezana-page-subtitle" aria-live="polite">
                            {{ $items->total() }} {{ __('items.plural') }}
                        </p>
                    </div>
                    @auth
                        <a href="{{ route('items.index') }}" class="khezana-btn khezana-btn-secondary">
                            {{ __('common.ui.my_items') }}
                        </a>
                    @endauth
                </div>
            </header>

            <!-- Main Content -->
            <main class="khezana-listing-main" role="main">
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
                        <nav class="khezana-pagination" aria-label="{{ __('common.ui.pagination') }}">
                            {{ $items->appends(request()->query())->links() }}
                        </nav>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="khezana-empty-state-modern" role="status" aria-live="polite">
                        <div class="khezana-empty-icon" aria-hidden="true">🔍</div>
                        <h2 class="khezana-empty-title">{{ __('common.messages.not_found') }}</h2>
                        <p class="khezana-empty-text">
                            {{ __('common.ui.no_results_message') }}
                        </p>
                        <div class="khezana-empty-actions">
                            <a href="{{ route('public.requests.create-info') }}" class="khezana-btn khezana-btn-primary">
                                {{ __('common.ui.no_results_cta_request') }}
                            </a>
                        </div>
                    </div>
                @endif
            </main>
        </div>
    </div>

@endsection
