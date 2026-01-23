{{-- Empty State Partial for Public Items --}}
{{-- Usage: @include('public.items._partials.empty-state') --}}

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
