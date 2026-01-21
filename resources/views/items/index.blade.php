@extends('layouts.app')

@section('title', 'عروضي - ' . config('app.name'))

@section('content')
    <div class="khezana-listing-page">
        <div class="khezana-container">
            <!-- Page Header -->
            <div class="khezana-page-header">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--khezana-spacing-md);">
                    <div>
                        <h1 class="khezana-page-title">عروضي</h1>
                        <p class="khezana-page-subtitle">
                            {{ $items->total() }} {{ __('items.plural') ?? 'إعلان' }}
                        </p>
                    </div>
                    <a href="{{ route('items.create') }}" class="khezana-btn khezana-btn-primary">
                        {{ __('common.ui.add_item') ?? 'أضف غرض جديد' }}
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <main class="khezana-listing-main" style="max-width: 100%;">
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
                                        {{ __('common.ui.no_image') ?? 'لا توجد صورة' }}
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
                                                {{ number_format($item->price, 0) }} ل.س
                                                @if ($item->operation_type->value == 'rent')
                                                    <span class="khezana-price-unit">/يوم</span>
                                                @endif
                                            </div>
                                        @endif
                                        <div style="display: flex; gap: var(--khezana-spacing-xs); flex-wrap: wrap;">
                                            <span class="khezana-item-badge khezana-item-badge-{{ $item->operation_type->value }}">
                                                @if ($item->operation_type->value == 'sell')
                                                    {{ __('items.operation_types.sell') ?? 'بيع' }}
                                                @elseif($item->operation_type->value == 'rent')
                                                    {{ __('items.operation_types.rent') ?? 'إيجار' }}
                                                @else
                                                    {{ __('items.operation_types.donate') ?? 'تبرع مجاني' }}
                                                @endif
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
                            {{ $items->links() }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="khezana-empty-state">
                        <div class="khezana-empty-icon">📦</div>
                        <h3 class="khezana-empty-title">لا توجد عروض</h3>
                        <p class="khezana-empty-text">
                            لم تقم بإضافة أي عروض بعد. ابدأ بإضافة أول عرض لك!
                        </p>
                        <a href="{{ route('items.create') }}" class="khezana-btn khezana-btn-primary">
                            {{ __('common.ui.add_item') ?? 'أضف غرض جديد' }}
                        </a>
                    </div>
                @endif
            </main>
        </div>
    </div>
@endsection
