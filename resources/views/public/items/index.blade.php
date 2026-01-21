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
                                {{ __('items.operation_types.sell') ?? 'بيع' }}
                            @elseif(request('operation_type') == 'rent')
                                {{ __('items.operation_types.rent') ?? 'إيجار' }}
                            @elseif(request('operation_type') == 'donate')
                                {{ __('items.operation_types.donate') ?? 'تبرع' }}
                            @else
                                {{ __('items.title') ?? 'جميع الإعلانات' }}
                            @endif
                        </h1>
                        <p class="khezana-page-subtitle">
                            {{ $items->total() }} {{ __('items.plural') ?? 'إعلان' }}
                        </p>
                    </div>
                    @auth
                        <a href="{{ route('items.index') }}" class="khezana-btn khezana-btn-secondary">
                            عروضي
                        </a>
                    @endauth
                </div>
            </div>

            <div class="khezana-listing-layout">
                <!-- Sidebar Filters -->
                <aside class="khezana-filters-sidebar">
                    <form method="GET" action="{{ route('public.items.index') }}" class="khezana-filters-form">
                        <!-- Preserve operation_type if exists -->
                        @if (request('operation_type'))
                            <input type="hidden" name="operation_type" value="{{ request('operation_type') }}">
                        @endif

                        <!-- Search -->
                        <div class="khezana-filter-group">
                            <label class="khezana-filter-label">{{ __('common.ui.search') ?? 'بحث' }}</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="{{ __('common.ui.search_items') ?? 'بحث في الإعلانات...' }}"
                                class="khezana-filter-input">
                        </div>

                        <!-- Operation Type -->
                        <div class="khezana-filter-group">
                            <label
                                class="khezana-filter-label">{{ __('items.fields.operation_type') ?? 'نوع العملية' }}</label>
                            <div class="khezana-filter-options">
                                <label class="khezana-filter-option">
                                    <input type="radio" name="operation_type" value="sell"
                                        {{ request('operation_type') == 'sell' ? 'checked' : '' }}
                                        onchange="this.form.submit()">
                                    <span>{{ __('items.operation_types.sell') ?? 'بيع' }}</span>
                                </label>
                                <label class="khezana-filter-option">
                                    <input type="radio" name="operation_type" value="rent"
                                        {{ request('operation_type') == 'rent' ? 'checked' : '' }}
                                        onchange="this.form.submit()">
                                    <span>{{ __('items.operation_types.rent') ?? 'إيجار' }}</span>
                                </label>
                                <label class="khezana-filter-option">
                                    <input type="radio" name="operation_type" value="donate"
                                        {{ request('operation_type') == 'donate' ? 'checked' : '' }}
                                        onchange="this.form.submit()">
                                    <span>{{ __('items.operation_types.donate') ?? 'تبرع' }}</span>
                                </label>
                                <label class="khezana-filter-option">
                                    <input type="radio" name="operation_type" value=""
                                        {{ !request('operation_type') ? 'checked' : '' }} onchange="this.form.submit()">
                                    <span>{{ __('common.ui.all') ?? 'الكل' }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Category -->
                        @if ($categories->count() > 0)
                            <div class="khezana-filter-group">
                                <label class="khezana-filter-label">{{ __('items.fields.category') ?? 'الفئة' }}</label>
                                <select name="category_id" class="khezana-filter-select" onchange="this.form.submit()">
                                    <option value="">{{ __('common.ui.all_categories') ?? 'جميع الفئات' }}</option>
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
                        @endif

                        <!-- Price Range (only for sell/rent) -->
                        @if (in_array(request('operation_type'), ['sell', 'rent']) || !request('operation_type'))
                            <div class="khezana-filter-group">
                                <label
                                    class="khezana-filter-label">{{ __('common.ui.price_range') ?? 'نطاق السعر' }}</label>
                                <div class="khezana-price-range">
                                    <input type="number" name="price_min" value="{{ request('price_min') }}"
                                        placeholder="{{ __('common.ui.min') ?? 'الحد الأدنى' }}"
                                        class="khezana-filter-input" min="0" step="0.01">
                                    <span class="khezana-price-separator">-</span>
                                    <input type="number" name="price_max" value="{{ request('price_max') }}"
                                        placeholder="{{ __('common.ui.max') ?? 'الحد الأقصى' }}"
                                        class="khezana-filter-input" min="0" step="0.01">
                                </div>
                            </div>
                        @endif

                        <!-- Sort -->
                        <div class="khezana-filter-group">
                            <label class="khezana-filter-label">{{ __('common.ui.filters') ?? 'ترتيب' }}</label>
                            <select name="sort" class="khezana-filter-select" onchange="this.form.submit()">
                                <option value="created_at_desc"
                                    {{ request('sort') == 'created_at_desc' ? 'selected' : '' }}>
                                    {{ __('common.ui.latest') ?? 'الأحدث' }}
                                </option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                                    {{ __('common.ui.price_low_to_high') ?? 'السعر: من الأقل إلى الأعلى' }}
                                </option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                                    {{ __('common.ui.price_high_to_low') ?? 'السعر: من الأعلى إلى الأقل' }}
                                </option>
                                <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>
                                    {{ __('common.ui.title_a_z') ?? 'العنوان: أ-ي' }}
                                </option>
                            </select>
                        </div>

                        <!-- Clear Filters -->
                        @if (request()->hasAny(['search', 'operation_type', 'category_id', 'price_min', 'price_max']))
                            <a href="{{ route('public.items.index') }}" class="khezana-btn khezana-btn-secondary"
                                style="width: 100%; margin-top: var(--khezana-spacing-md);">
                                {{ __('common.ui.clear_filters') ?? 'مسح الفلاتر' }}
                            </a>
                        @endif
                    </form>
                </aside>

                <!-- Main Content -->
                <main class="khezana-listing-main">
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
                                            {{ __('common.ui.no_image') ?? 'لا توجد صورة' }}
                                        </div>
                                    @endif
                                    <div class="khezana-item-content">
                                        <h3 class="khezana-item-title">{{ $item->title }}</h3>
                                        @if ($item->category)
                                            <p class="khezana-item-category">{{ $item->category->name }}</p>
                                        @endif
                                        <div class="khezana-item-footer">
                                            @if ($item->price && $item->operationType != 'donate')
                                                <div class="khezana-item-price">
                                                    {{ number_format($item->price, 0) }} ل.س
                                                    @if ($item->operationType == 'rent')
                                                        <span class="khezana-price-unit">/يوم</span>
                                                    @endif
                                                </div>
                                            @endif
                                            <span class="khezana-item-badge khezana-item-badge-{{ $item->operationType }}">
                                                @if ($item->operationType == 'sell')
                                                    {{ __('items.operation_types.sell') ?? 'بيع' }}
                                                @elseif($item->operationType == 'rent')
                                                    {{ __('items.operation_types.rent') ?? 'إيجار' }}
                                                @else
                                                    {{ __('items.operation_types.donate') ?? 'تبرع مجاني' }}
                                                @endif
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
                            <div class="khezana-empty-icon">📦</div>
                            <h3 class="khezana-empty-title">{{ __('common.messages.not_found') ?? 'لا توجد نتائج' }}</h3>
                            <p class="khezana-empty-text">
                                لم نجد إعلانات تطابق معايير البحث الخاصة بك. جرب تغيير الفلاتر.
                            </p>
                            @if (request()->hasAny(['search', 'operation_type', 'category_id', 'price_min', 'price_max']))
                                <a href="{{ route('public.items.index') }}" class="khezana-btn khezana-btn-primary">
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
