{{-- Public Item Branch Info Partial --}}
{{-- Usage: @include('public.items._partials.detail.info', ['branch' => $branch, 'mapsUrl' => $mapsUrl]) --}}

<div class="khezana-branch-info">
    <div class="khezana-branch-info__header">
        <span class="khezana-branch-info__icon">&#x1F4CD;</span>
        <span class="khezana-branch-info__label">{{ __('items.available_at_branch') }}</span>
    </div>

    <div class="khezana-branch-info__details">
        <div class="khezana-branch-info__name">
            <strong>{{ $branch->name }}</strong>
        </div>

        @if ($branch->city)
            <div class="khezana-branch-info__city">
                <span class="khezana-branch-info__detail-icon">&#x1F3D9;&#xFE0F;</span>
                {{ $branch->city }}
            </div>
        @endif

        @if ($branch->address)
            <div class="khezana-branch-info__address">
                <span class="khezana-branch-info__detail-icon">&#x1F4EE;</span>
                {{ $branch->address }}
            </div>
        @endif

        @if ($branch->phone)
            <div class="khezana-branch-info__phone">
                <span class="khezana-branch-info__detail-icon">&#x1F4DE;</span>
                <a href="tel:{{ $branch->phone }}" dir="ltr">{{ $branch->phone }}</a>
            </div>
        @endif
    </div>

    @if ($mapsUrl)
        <a href="{{ $mapsUrl }}"
           target="_blank"
           rel="noopener noreferrer"
           class="khezana-btn khezana-btn-secondary khezana-btn-large khezana-btn-full">
            <span class="khezana-btn__icon">&#x1F5FA;&#xFE0F;</span>
            {{ __('items.get_directions') }}
        </a>
    @endif
</div>
