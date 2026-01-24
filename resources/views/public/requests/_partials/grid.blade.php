{{-- Public Requests Grid Partial --}}
{{-- Usage: @include('public.requests._partials.grid', ['requests' => $requests]) --}}

@php
    use App\ViewModels\Requests\RequestCardViewModel;
@endphp

<div class="khezana-requests-grid" role="list">
    @foreach ($requests as $request)
        @php
            $viewModel = RequestCardViewModel::fromRequest($request, 'public');
        @endphp
        <a href="{{ $viewModel->url }}" class="khezana-request-card" role="listitem">
            <div class="khezana-request-content">
                <div class="khezana-request-header">
                    <h3 class="khezana-request-title">{{ $viewModel->title }}</h3>
                    <span class="khezana-request-badge {{ $viewModel->statusBadgeClass }}">
                        {{ $viewModel->statusLabel }}
                    </span>
                </div>

                @if ($viewModel->category)
                    <p class="khezana-request-category">{{ $viewModel->category }}</p>
                @endif

                @if ($viewModel->descriptionPreview)
                    <p class="khezana-request-description">
                        {{ $viewModel->descriptionPreview }}
                    </p>
                @endif

                @if ($viewModel->hasAttributes)
                    <div class="khezana-request-attributes">
                        @foreach ($viewModel->displayAttributes as $attr)
                            <span class="khezana-request-attribute">
                                <strong>{{ $attr['name'] ?? translate_attribute_name($attr['originalName'] ?? '') }}:</strong>
                                {{ $attr['value'] }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <div class="khezana-request-footer">
                    <div class="khezana-request-meta">
                        <span class="khezana-request-date">
                            {{ __('common.ui.posted') }} {{ $viewModel->createdAtFormatted }}
                        </span>
                    </div>
                    @if ($viewModel->hasOffers)
                        <span class="khezana-request-offers">
                            {{ $viewModel->offersText }}
                        </span>
                    @endif
                </div>
            </div>
        </a>
    @endforeach
</div>
