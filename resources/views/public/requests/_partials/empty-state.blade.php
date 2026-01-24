{{-- Empty State Partial for Public Requests --}}
{{-- Usage: @include('public.requests._partials.empty-state') --}}

<div class="khezana-empty-state-modern" role="status" aria-live="polite">
    <div class="khezana-empty-icon" aria-hidden="true">📝</div>
    <h2 class="khezana-empty-title">{{ __('requests.messages.no_requests') ?? 'لا توجد طلبات حالياً' }}</h2>
    <p class="khezana-empty-text">
        {{ __('common.ui.no_requests_message') ?? 'لم يتم نشر أي طلبات بعد. كن أول من ينشر طلباً!' }}
    </p>
    <div class="khezana-empty-actions">
        @auth
            <a href="{{ route('requests.create') }}" class="khezana-btn khezana-btn-primary khezana-btn-large">
                {{ __('common.ui.create_new_request') ?? 'إنشاء طلب جديد' }}
            </a>
        @else
            <a href="{{ route('public.requests.create-info') }}" class="khezana-btn khezana-btn-primary khezana-btn-large">
                {{ __('common.ui.create_new_request') ?? 'إنشاء طلب جديد' }}
            </a>
        @endauth
    </div>
</div>
