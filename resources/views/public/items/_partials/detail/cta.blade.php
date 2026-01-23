{{-- Public Item CTA Partial --}}
{{-- Usage: @include('public.items._partials.detail.cta', ['viewModel' => $viewModel]) --}}

<div class="khezana-item-cta">
    @auth
        <button type="button" onclick="showContactForm()"
            class="khezana-btn khezana-btn-primary khezana-btn-large khezana-btn-full">
            {{ __('common.ui.contact_seller') }}
        </button>
    @else
        <a href="{{ route('login') }}"
            class="khezana-btn khezana-btn-primary khezana-btn-large khezana-btn-full">
            {{ __('common.ui.register_to_continue') }}
        </a>
    @endauth
</div>
