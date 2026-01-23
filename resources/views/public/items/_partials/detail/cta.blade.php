{{-- Public Item CTA Partial --}}
{{-- Usage: @include('public.items._partials.detail.cta', ['viewModel' => $viewModel]) --}}

@php
    // Check if current user is the item owner
    $isOwner = false;
    if (auth()->check()) {
        $item = \App\Models\Item::find($viewModel->itemId);
        $isOwner = $item && $item->user_id === auth()->id();
    }
@endphp

<div class="khezana-item-cta">
    @auth
        @if (!$isOwner)
            <button type="button" onclick="showContactForm()"
                class="khezana-btn khezana-btn-primary khezana-btn-large khezana-btn-full">
                {{ __('common.ui.contact_seller') }}
            </button>
        @endif
    @else
        <a href="{{ route('login') }}"
            class="khezana-btn khezana-btn-primary khezana-btn-large khezana-btn-full">
            {{ __('common.ui.register_to_continue') }}
        </a>
    @endauth
</div>
