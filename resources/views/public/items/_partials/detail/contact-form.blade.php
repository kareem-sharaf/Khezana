{{-- Contact Form Partial --}}
{{-- Usage: @include('public.items._partials.detail.contact-form', ['viewModel' => $viewModel]) --}}

@php
    // Check if current user is the item owner
    $isOwner = false;
    if (auth()->check()) {
        $item = \App\Models\Item::find($viewModel->itemId);
        $isOwner = $item && $item->user_id === auth()->id();
    }
@endphp

@auth
    @if (!$isOwner)
        <div id="contactForm" class="khezana-contact-form" style="display: none;">
            <h3 class="khezana-section-title-small">{{ __('common.ui.contact_seller') }}</h3>
            <form method="POST" action="{{ route('public.items.contact', $viewModel->itemId) }}">
                @csrf
                <div class="khezana-form-group">
                    <label class="khezana-form-label">{{ __('common.ui.your_message') }}</label>
                    <textarea name="message" class="khezana-form-input" rows="4" required placeholder="{{ __('common.ui.write_your_message') }}"></textarea>
                </div>
                <div class="khezana-form-actions">
                    <button type="submit" class="khezana-btn khezana-btn-primary">
                        {{ __('common.actions.save') }}
                    </button>
                    <button type="button" onclick="hideContactForm()"
                        class="khezana-btn khezana-btn-secondary">
                        {{ __('common.actions.cancel') }}
                    </button>
                </div>
            </form>
        </div>
    @endif
@endauth
