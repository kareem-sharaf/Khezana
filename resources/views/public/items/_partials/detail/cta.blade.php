{{-- Public Item CTA Partial --}}
{{-- Usage: @include('public.items._partials.detail.cta', ['viewModel' => $viewModel]) --}}

@php
    // Check if current user is the item owner
    $isOwner = false;
    $item = null;
    if (auth()->check()) {
        $item = \App\Models\Item::with('branch')->find($viewModel->itemId);
        $isOwner = $item && $item->user_id === auth()->id();
    }
    
    // Get item if not already loaded
    if (!$item) {
        $item = \App\Models\Item::with('branch')->find($viewModel->itemId);
    }
    
    // Check if item is in a branch
    $isInBranch = $item && $item->isInBranch();
    $branch = $item?->branch;
    
    // Platform contact configuration (for items with seller)
    $platformWhatsapp = config('services.platform.whatsapp', '+963959378002');
    $platformTelegram = config('services.platform.telegram_username', 'khezana_support');
    
    // Build message with item details
    $itemUrl = $viewModel->url;
    $contactMessage = __('items.contact_platform_message', [
        'title' => $viewModel->title,
        'url' => $itemUrl,
    ]);
    
    $whatsappUrl = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $platformWhatsapp) . '?text=' . urlencode($contactMessage);
    $telegramUrl = 'https://t.me/' . $platformTelegram . '?text=' . urlencode($contactMessage);
    
    // Google Maps URL for branch location
    $mapsUrl = null;
    if ($branch && $branch->latitude && $branch->longitude) {
        $mapsUrl = "https://www.google.com/maps?q={$branch->latitude},{$branch->longitude}";
    } elseif ($branch && $branch->address) {
        $mapsUrl = "https://www.google.com/maps/search/" . urlencode($branch->full_address);
    }
@endphp

<div class="khezana-item-cta">
    @if ($isInBranch && $branch)
        @include('public.items._partials.detail.info', [
            'branch' => $branch,
            'mapsUrl' => $mapsUrl,
        ])
    @else
        {{-- Item is with seller - Show platform contact --}}
        @auth
            @if (!$isOwner)
                <div class="khezana-platform-contact">
                    <p class="khezana-platform-contact__intro">
                        {{ __('items.contact_platform_for_item') }}
                    </p>
                    
                    {{-- WhatsApp Button --}}
                    <a href="{{ $whatsappUrl }}" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="khezana-btn khezana-btn-whatsapp khezana-btn-large khezana-btn-full js-contact-track"
                       data-item-id="{{ $viewModel->itemId }}"
                       data-channel="whatsapp">
                        <svg class="khezana-btn-whatsapp__icon" fill="currentColor" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        <span>{{ __('items.contact_whatsapp') }}</span>
                    </a>
                    
                    {{-- Telegram Button --}}
                    <a href="{{ $telegramUrl }}" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="khezana-btn khezana-btn-telegram khezana-btn-large khezana-btn-full js-contact-track"
                       data-item-id="{{ $viewModel->itemId }}"
                       data-channel="telegram">
                        <svg class="khezana-btn-telegram__icon" fill="currentColor" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
                            <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                        </svg>
                        <span>{{ __('items.contact_telegram') }}</span>
                    </a>
                </div>
            @else
                {{-- Owner message --}}
                <p class="khezana-item-cta__owner-notice">
                    {{ __('common.ui.this_is_your_item') }}
                </p>
            @endif
        @else
            {{-- Guest message - Encourage login --}}
            <div class="khezana-item-cta__guest-notice">
                <p>{{ __('common.ui.login_to_contact') }}</p>
                <a href="{{ route('login', ['redirect' => url()->current()]) }}" 
                   class="khezana-btn khezana-btn-primary khezana-btn-large khezana-btn-full">
                    {{ __('common.ui.login') }}
                </a>
                <p class="khezana-item-cta__register-hint">
                    {{ __('common.ui.no_account') }} 
                    <a href="{{ route('register') }}">{{ __('common.ui.register_now') }}</a>
                </p>
            </div>
        @endauth
    @endif
</div>

@auth
@push('scripts')
<script>
    // Contact Interaction Tracking using Beacon API
    document.addEventListener('DOMContentLoaded', function() {
        const trackButtons = document.querySelectorAll('.js-contact-track');
        const trackUrl = '{{ route('contact.track') }}';
        const csrfToken = '{{ csrf_token() }}';
        
        trackButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const itemId = this.dataset.itemId;
                const channel = this.dataset.channel;
                
                // Prepare tracking data
                const data = new FormData();
                data.append('item_id', itemId);
                data.append('channel', channel);
                data.append('_token', csrfToken);
                
                // Use Beacon API for reliable tracking (works even if page closes)
                if (navigator.sendBeacon) {
                    navigator.sendBeacon(trackUrl, data);
                } else {
                    // Fallback for older browsers
                    fetch(trackUrl, {
                        method: 'POST',
                        body: data,
                        keepalive: true
                    }).catch(function() {
                        // Silently fail - don't block user experience
                    });
                }
            });
        });
    });
</script>
@endpush
@endauth

<style>
.khezana-branch-info {
    background: var(--khezana-bg-secondary, #f8f9fa);
    border-radius: var(--khezana-radius-lg, 12px);
    padding: var(--khezana-spacing-lg, 1.5rem);
    margin-bottom: var(--khezana-spacing-md, 1rem);
}

.khezana-branch-info__header {
    display: flex;
    align-items: center;
    gap: var(--khezana-spacing-sm, 0.5rem);
    margin-bottom: var(--khezana-spacing-md, 1rem);
    font-size: 1.1rem;
    color: var(--khezana-success, #28a745);
}

.khezana-branch-info__icon {
    font-size: 1.5rem;
}

.khezana-branch-info__details {
    margin-bottom: var(--khezana-spacing-md, 1rem);
}

.khezana-branch-info__name {
    font-size: 1.2rem;
    margin-bottom: var(--khezana-spacing-sm, 0.5rem);
}

.khezana-branch-info__city,
.khezana-branch-info__address,
.khezana-branch-info__phone {
    display: flex;
    align-items: center;
    gap: var(--khezana-spacing-xs, 0.25rem);
    margin-bottom: var(--khezana-spacing-xs, 0.25rem);
    color: var(--khezana-text-secondary, #666);
}

.khezana-branch-info__detail-icon {
    width: 1.5rem;
}

.khezana-branch-info__phone a {
    color: var(--khezana-primary, #4a90a4);
    text-decoration: none;
}

.khezana-branch-info__phone a:hover {
    text-decoration: underline;
}

.khezana-platform-contact {
    text-align: center;
}

.khezana-platform-contact__intro {
    margin-bottom: var(--khezana-spacing-md, 1rem);
    color: var(--khezana-text-secondary, #666);
}
</style>


