{{-- Empty State Partial for Public Requests --}}
{{-- Usage: @include('public.requests._partials.empty-state') --}}

<div class="khezana-empty-state" role="status" aria-live="polite">
    <div class="khezana-empty-icon" aria-hidden="true">🔍</div>
    <h3 class="khezana-empty-title">{{ __('common.messages.not_found') }}</h3>
    <p class="khezana-empty-text">
        {{ __('common.ui.no_results_message') }}
    </p>
    <div class="khezana-empty-actions">
        @auth
            <a href="{{ route('requests.create') }}" class="khezana-btn khezana-btn-primary">
                {{ __('common.ui.no_results_cta_request') }}
            </a>
        @else
            <a href="{{ route('public.requests.create-info') }}" class="khezana-btn khezana-btn-primary">
                {{ __('common.ui.no_results_cta_request') }}
            </a>
        @endauth
    </div>
</div>
