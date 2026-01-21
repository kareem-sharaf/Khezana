@extends('layouts.app')

@section('title', $requestModel->title . ' - ' . config('app.name'))

@section('content')
<div class="khezana-container khezana-request-show">
    <div class="khezana-breadcrumb">
        <a href="{{ route('requests.index') }}" class="khezana-link">{{ __('common.ui.my_requests_page') }}</a>
        <span class="khezana-breadcrumb-sep">/</span>
        <span>{{ $requestModel->title }}</span>
    </div>

    <div class="khezana-card">
        <div class="khezana-card-header">
            <div>
                <h1 class="khezana-page-title">{{ $requestModel->title }}</h1>
                <p class="khezana-text-muted">{{ $requestModel->category?->name ?? __('common.ui.unknown') }}</p>
            </div>
            <div class="khezana-badges">
                <span class="khezana-badge khezana-badge-primary">
                    {{ $requestModel->status->label() }}
                </span>
            </div>
            @if ($requestModel->status->value == 'open')
                <p class="khezana-status-explanation" style="margin-top: var(--khezana-spacing-xs);">
                    {{ __('requests.detail.status_explanation_open') }}
                </p>
            @elseif($requestModel->status->value == 'fulfilled')
                <p class="khezana-status-explanation" style="margin-top: var(--khezana-spacing-xs);">
                    {{ __('requests.detail.status_explanation_fulfilled') }}
                </p>
            @else
                <p class="khezana-status-explanation" style="margin-top: var(--khezana-spacing-xs);">
                    {{ __('requests.detail.status_explanation_closed') }}
                </p>
            @endif
        </div>

        <div class="khezana-card-body">
            <h3 class="khezana-section-title-small">{{ __('common.ui.description') }}</h3>
            <p class="khezana-text-body">{{ $requestModel->description ?: __('requests.messages.no_description') }}</p>

            @if($requestModel->itemAttributes && $requestModel->itemAttributes->count())
                <div class="khezana-attributes-grid">
                    @foreach($requestModel->itemAttributes as $attr)
                        <div class="khezana-attribute-item">
                            <div class="khezana-attribute-name">{{ $attr->attribute?->name ?? __('items.fields.attributes') }}</div>
                            <div class="khezana-attribute-value">{{ $attr->value }}</div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Next Steps Guide -->
            <div class="khezana-next-steps" style="margin-top: var(--khezana-spacing-xl);">
                <h3 class="khezana-section-title-small">{{ __('requests.detail.next_steps_title') }}</h3>
                <div class="khezana-next-steps-content">
                    @if ($requestModel->status->value == 'open')
                        <div class="khezana-next-step-item">
                            <div class="khezana-next-step-icon">✅</div>
                            <div class="khezana-next-step-text">
                                <p class="khezana-next-step-main">{{ __('requests.detail.next_steps_open') }}</p>
                                <p class="khezana-next-step-hint">{{ __('requests.detail.next_steps_open_hint') }}</p>
                            </div>
                        </div>
                        @if($requestModel->offers->count() == 0)
                            <div class="khezana-next-step-item">
                                <div class="khezana-next-step-icon">⏳</div>
                                <div class="khezana-next-step-text">
                                    <p class="khezana-next-step-main">{{ __('requests.detail.offers_coming') }}</p>
                                </div>
                            </div>
                        @else
                            <div class="khezana-next-step-item">
                                <div class="khezana-next-step-icon">📋</div>
                                <div class="khezana-next-step-text">
                                    <p class="khezana-next-step-main">{{ __('requests.detail.review_offers') }}</p>
                                </div>
                            </div>
                            <div class="khezana-next-step-item">
                                <div class="khezana-next-step-icon">💬</div>
                                <div class="khezana-next-step-text">
                                    <p class="khezana-next-step-main">{{ __('requests.detail.contact_offerer') }}</p>
                                </div>
                            </div>
                        @endif
                    @elseif($requestModel->status->value == 'fulfilled')
                        <div class="khezana-next-step-item">
                            <div class="khezana-next-step-icon">🎉</div>
                            <div class="khezana-next-step-text">
                                <p class="khezana-next-step-main">{{ __('requests.detail.next_steps_fulfilled') }}</p>
                            </div>
                        </div>
                    @else
                        <div class="khezana-next-step-item">
                            <div class="khezana-next-step-icon">🔒</div>
                            <div class="khezana-next-step-text">
                                <p class="khezana-next-step-main">{{ __('requests.detail.next_steps_closed') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="khezana-card-footer">
            <span class="khezana-text-muted">
                {{ __('common.ui.published') }}: {{ $requestModel->created_at?->format('Y-m-d H:i') }}
            </span>
        </div>
    </div>
</div>
@endsection
