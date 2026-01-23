{{-- Public Requests Grid Partial --}}
{{-- Usage: @include('public.requests._partials.grid', ['requests' => $requests]) --}}

@php
    use Illuminate\Support\Str;
@endphp

<div class="khezana-requests-grid" role="list">
    @foreach ($requests as $request)
        <a href="{{ $request->url }}" class="khezana-request-card" role="listitem">
            <div class="khezana-request-content">
                <div class="khezana-request-header">
                    <h3 class="khezana-request-title">{{ $request->title }}</h3>
                    <span class="khezana-request-badge khezana-request-badge-{{ $request->status }}">
                        {{ $request->statusLabel }}
                    </span>
                </div>

                @if ($request->category)
                    <p class="khezana-request-category">{{ $request->category->name }}</p>
                @endif

                @if ($request->description)
                    <p class="khezana-request-description">
                        {{ Str::limit($request->description, 120) }}
                    </p>
                @endif

                @if ($request->attributes->count() > 0)
                    <div class="khezana-request-attributes">
                        @foreach ($request->attributes->take(3) as $attr)
                            <span class="khezana-request-attribute">
                                <strong>{{ $attr->name }}:</strong> {{ $attr->value }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <div class="khezana-request-footer">
                    <div class="khezana-request-meta">
                        @if ($request->user)
                            <span class="khezana-request-user">
                                {{ $request->user->name }}
                            </span>
                        @endif
                        <span class="khezana-request-date">
                            {{ __('common.ui.posted') }} {{ $request->createdAtFormatted }}
                        </span>
                    </div>
                    @if ($request->offersCount > 0)
                        <span class="khezana-request-offers">
                            {{ $request->offersCount }} {{ __('common.ui.offers') }}
                        </span>
                    @endif
                </div>
            </div>
        </a>
    @endforeach
</div>
