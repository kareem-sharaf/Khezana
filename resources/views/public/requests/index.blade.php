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
                            {{ __('requests.title') ?? 'الطلبات' }}
                        </h1>
                        <p class="khezana-page-subtitle">
                            {{ $requests->total() }} {{ __('requests.plural') ?? 'طلب' }}
                        </p>
                    </div>
                    @auth
                        <a href="{{ route('requests.index') }}" class="khezana-btn khezana-btn-secondary">
                            طلباتي
                        </a>
                    @endauth
                </div>
            </div>

            <div class="khezana-listing-layout">
                <!-- Sidebar Filters -->
                <aside class="khezana-filters-sidebar">
                    <form method="GET" action="{{ route('public.requests.index') }}" class="khezana-filters-form">
                        <!-- Search -->
                        <div class="khezana-filter-group">
                            <label class="khezana-filter-label">{{ __('common.ui.search') ?? 'بحث' }}</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="{{ __('common.ui.search_requests') ?? 'بحث في الطلبات...' }}"
                                class="khezana-filter-input">
                        </div>

                        <!-- Status -->
                        <div class="khezana-filter-group">
                            <label class="khezana-filter-label">{{ __('requests.fields.status') ?? 'الحالة' }}</label>
                            <select name="status" class="khezana-filter-select" onchange="this.form.submit()">
                                <option value="">{{ __('common.ui.all') ?? 'الكل' }}</option>
                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>
                                    {{ __('requests.status.open') ?? 'مفتوح' }}
                                </option>
                                <option value="fulfilled" {{ request('status') == 'fulfilled' ? 'selected' : '' }}>
                                    {{ __('requests.status.fulfilled') ?? 'مكتمل' }}
                                </option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>
                                    {{ __('requests.status.closed') ?? 'مغلق' }}
                                </option>
                            </select>
                        </div>

                        <!-- Sort -->
                        <div class="khezana-filter-group">
                            <label class="khezana-filter-label">{{ __('common.ui.filters') ?? 'ترتيب' }}</label>
                            <select name="sort" class="khezana-filter-select" onchange="this.form.submit()">
                                <option value="created_at_desc"
                                    {{ request('sort') == 'created_at_desc' ? 'selected' : '' }}>
                                    {{ __('common.ui.latest') ?? 'الأحدث' }}
                                </option>
                                <option value="created_at_asc" {{ request('sort') == 'created_at_asc' ? 'selected' : '' }}>
                                    {{ __('common.ui.oldest') ?? 'الأقدم' }}
                                </option>
                                <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>
                                    {{ __('common.ui.title_a_z') ?? 'العنوان: أ-ي' }}
                                </option>
                            </select>
                        </div>

                        <!-- Clear Filters -->
                        @if (request()->hasAny(['search', 'status']))
                            <a href="{{ route('public.requests.index') }}" class="khezana-btn khezana-btn-secondary"
                                style="width: 100%; margin-top: var(--khezana-spacing-md);">
                                {{ __('common.ui.clear_filters') ?? 'مسح الفلاتر' }}
                            </a>
                        @endif
                    </form>
                </aside>

                <!-- Main Content -->
                <main class="khezana-listing-main">
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
                                                    {{ $request->createdAtFormatted }}
                                                </span>
                                            </div>
                                            @if ($request->offersCount > 0)
                                                <span class="khezana-request-offers">
                                                    {{ $request->offersCount }} {{ __('requests.offers') ?? 'عرض' }}
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
                            <div class="khezana-empty-icon">📝</div>
                            <h3 class="khezana-empty-title">{{ __('common.messages.not_found') ?? 'لا توجد نتائج' }}</h3>
                            <p class="khezana-empty-text">
                                لم نجد طلبات تطابق معايير البحث الخاصة بك. جرب تغيير الفلاتر.
                            </p>
                            @if (request()->hasAny(['search', 'status']))
                                <a href="{{ route('public.requests.index') }}" class="khezana-btn khezana-btn-primary">
                                    {{ __('common.ui.clear_filters') ?? 'مسح الفلاتر' }}
                                </a>
                            @endif
                        </div>
                    @endif
                </main>
            </div>
        </div>
    </div>
@endsection
