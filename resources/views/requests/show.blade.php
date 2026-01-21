@extends('layouts.app')

@section('title', $requestModel->title . ' - ' . config('app.name'))

@section('content')
<div class="khezana-container khezana-request-show">
    <div class="khezana-breadcrumb">
        <a href="{{ route('requests.index') }}" class="khezana-link">{{ __('requests.title') ?? 'طلباتي' }}</a>
        <span class="khezana-breadcrumb-sep">/</span>
        <span>{{ $requestModel->title }}</span>
    </div>

    <div class="khezana-card">
        <div class="khezana-card-header">
            <div>
                <h1 class="khezana-page-title">{{ $requestModel->title }}</h1>
                <p class="khezana-text-muted">{{ $requestModel->category?->name ?? 'بدون فئة' }}</p>
            </div>
            <div class="khezana-badges">
                <span class="khezana-badge khezana-badge-primary">
                    {{ $requestModel->status->label() ?? 'حالة غير معروفة' }}
                </span>
            </div>
        </div>

        <div class="khezana-card-body">
            <h3 class="khezana-section-title-small">الوصف</h3>
            <p class="khezana-text-body">{{ $requestModel->description ?: 'لا يوجد وصف' }}</p>

            @if($requestModel->itemAttributes && $requestModel->itemAttributes->count())
                <div class="khezana-attributes-grid">
                    @foreach($requestModel->itemAttributes as $attr)
                        <div class="khezana-attribute-item">
                            <div class="khezana-attribute-name">{{ $attr->attribute?->name ?? 'خاصية' }}</div>
                            <div class="khezana-attribute-value">{{ $attr->value }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="khezana-card-footer">
            <span class="khezana-text-muted">
                {{ __('requests.created_at') ?? 'تم الإنشاء' }}: {{ $requestModel->created_at?->format('Y-m-d H:i') }}
            </span>
        </div>
    </div>
</div>
@endsection
