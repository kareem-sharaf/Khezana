@extends('layouts.app')

@section('title', $item->title . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-item-detail-page">
        <div class="khezana-container">
            <!-- Breadcrumb -->
            <nav class="khezana-breadcrumb">
                <a href="{{ route('home') }}">الرئيسية</a>
                <span>/</span>
                <a href="{{ route('items.index') }}">عناصري</a>
                <span>/</span>
                <span>{{ $item->title }}</span>
            </nav>

            <div class="khezana-item-detail-layout">
                <!-- Images Section -->
                <div class="khezana-item-images">
                    @if ($item->images->count() > 0)
                        <!-- Main Image -->
                        <div class="khezana-item-main-image">
                            @php
                                $primaryImage =
                                    $item->images->where('is_primary', true)->first() ?? $item->images->first();
                            @endphp
                            <img id="mainImage" src="{{ asset('storage/' . $primaryImage->path) }}" alt="{{ $item->title }}"
                                class="khezana-main-img" loading="eager">
                        </div>

                        <!-- Thumbnails (if more than one image) -->
                        @if ($item->images->count() > 1)
                            <div class="khezana-item-thumbnails">
                                @foreach ($item->images as $image)
                                    <button type="button" class="khezana-thumbnail {{ $loop->first ? 'active' : '' }}"
                                        onclick="changeMainImage('{{ asset('storage/' . $image->path) }}', this)">
                                        <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $item->title }}"
                                            loading="lazy">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="khezana-item-main-image khezana-no-image">
                            <div class="khezana-no-image-placeholder">
                                {{ __('common.ui.no_image') ?? 'لا توجد صورة' }}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Details Section -->
                <div class="khezana-item-details">
                    <!-- Header -->
                    <div class="khezana-item-header">
                        <h1 class="khezana-item-detail-title">{{ $item->title }}</h1>
                        <div style="display: flex; gap: var(--khezana-spacing-sm); flex-wrap: wrap;">
                            <span class="khezana-item-badge khezana-item-badge-{{ $item->operation_type->value }}">
                                @if ($item->operation_type->value == 'sell')
                                    {{ __('items.operation_types.sell') ?? 'بيع' }}
                                @elseif($item->operation_type->value == 'rent')
                                    {{ __('items.operation_types.rent') ?? 'إيجار' }}
                                @else
                                    {{ __('items.operation_types.donate') ?? 'تبرع مجاني' }}
                                @endif
                            </span>

                            @if ($item->approvalRelation)
                                @php
                                    $status = $item->approvalRelation->status;
                                    $statusClass = match ($status->value) {
                                        'approved' => 'khezana-approval-badge-approved',
                                        'pending' => 'khezana-approval-badge-pending',
                                        'rejected' => 'khezana-approval-badge-rejected',
                                        'archived' => 'khezana-approval-badge-archived',
                                        default => 'khezana-approval-badge-pending',
                                    };
                                @endphp
                                <span class="khezana-approval-badge {{ $statusClass }}">
                                    {{ $status->label() }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="khezana-item-price-section">
                        @if ($item->operation_type->value == 'donate')
                            <div class="khezana-item-price khezana-item-price-free">
                                <span class="khezana-price-label">مجاني</span>
                            </div>
                        @elseif($item->price)
                            <div class="khezana-item-price">
                                <span class="khezana-price-amount">{{ number_format($item->price, 0) }}</span>
                                <span class="khezana-price-currency">ل.س</span>
                                @if ($item->operation_type->value == 'rent')
                                    <span class="khezana-price-unit">/يوم</span>
                                @endif
                            </div>
                            @if ($item->operation_type->value == 'rent' && $item->deposit_amount)
                                <div class="khezana-item-deposit">
                                    <span class="khezana-deposit-label">عربون:</span>
                                    <span class="khezana-deposit-amount">{{ number_format($item->deposit_amount, 0) }}
                                        ل.س</span>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Category -->
                    @if ($item->category)
                        <div class="khezana-item-meta">
                            <span class="khezana-meta-label">الفئة:</span>
                            <span class="khezana-meta-value">{{ $item->category->name }}</span>
                        </div>
                    @endif

                    <!-- Availability -->
                    <div class="khezana-item-meta">
                        <span class="khezana-meta-label">الحالة:</span>
                        <span class="khezana-meta-value">
                            {{ $item->is_available ? 'متاح' : 'غير متاح' }}
                        </span>
                    </div>

                    <!-- Attributes -->
                    @if ($item->itemAttributes->count() > 0)
                        <div class="khezana-item-attributes">
                            <h3 class="khezana-section-title-small">المواصفات</h3>
                            <div class="khezana-attributes-grid">
                                @foreach ($item->itemAttributes as $itemAttribute)
                                    <div class="khezana-attribute-item">
                                        <span class="khezana-attribute-name">{{ $itemAttribute->attribute->name }}:</span>
                                        <span class="khezana-attribute-value">{{ $itemAttribute->value }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Description -->
                    @if ($item->description)
                        <div class="khezana-item-description">
                            <h3 class="khezana-section-title-small">الوصف</h3>
                            <p class="khezana-description-text">{{ $item->description }}</p>
                        </div>
                    @endif

                    <!-- Approval Status Info -->
                    @if ($item->approvalRelation)
                        <div class="khezana-approval-info">
                            @if ($item->approvalRelation->status->value == 'pending')
                                <div class="khezana-info-box khezana-info-box-warning">
                                    <strong>⏳ قيد المراجعة:</strong> إعلانك قيد المراجعة من قبل فريقنا. سيتم إشعارك عند
                                    الموافقة أو الرفض.
                                </div>
                            @elseif($item->approvalRelation->status->value == 'rejected')
                                <div class="khezana-info-box khezana-info-box-error">
                                    <strong>❌ تم الرفض:</strong>
                                    @if ($item->approvalRelation->rejection_reason)
                                        {{ $item->approvalRelation->rejection_reason }}
                                    @else
                                        تم رفض إعلانك. يمكنك تعديله وإعادة الإرسال للمراجعة.
                                    @endif
                                </div>
                            @elseif($item->approvalRelation->status->value == 'approved')
                                <div class="khezana-info-box khezana-info-box-success">
                                    <strong>✅ تمت الموافقة:</strong> إعلانك معروض الآن للجمهور.
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="khezana-item-actions">
                        @if (!$item->isPending() && !$item->isApproved())
                            <form method="POST" action="{{ route('items.submit-for-approval', $item) }}"
                                style="display: inline;">
                                @csrf
                                <button type="submit" class="khezana-btn khezana-btn-primary">
                                    إرسال للمراجعة
                                </button>
                            </form>
                        @endif

                        @if (!$item->isPending())
                            <a href="{{ route('items.edit', $item) }}" class="khezana-btn khezana-btn-secondary">
                                تعديل
                            </a>
                        @endif

                        <form method="POST" action="{{ route('items.destroy', $item) }}" style="display: inline;"
                            onsubmit="return confirm('هل أنت متأكد من حذف هذا الإعلان؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="khezana-btn khezana-btn-ghost"
                                style="color: var(--khezana-danger);">
                                حذف
                            </button>
                        </form>
                    </div>

                    <!-- Additional Info -->
                    <div class="khezana-item-additional-info">
                        <div class="khezana-info-item">
                            <span class="khezana-info-icon">📅</span>
                            <span class="khezana-info-text">نُشر {{ $item->created_at->diffForHumans() }}</span>
                        </div>
                        @if ($item->updated_at != $item->created_at)
                            <div class="khezana-info-item">
                                <span class="khezana-info-icon">🔄</span>
                                <span class="khezana-info-text">آخر تحديث {{ $item->updated_at->diffForHumans() }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple JavaScript for Image Gallery -->
    <script>
        function changeMainImage(src, element) {
            document.getElementById('mainImage').src = src;

            // Update active thumbnail
            document.querySelectorAll('.khezana-thumbnail').forEach(thumb => {
                thumb.classList.remove('active');
            });
            element.classList.add('active');
        }
    </script>
@endsection
