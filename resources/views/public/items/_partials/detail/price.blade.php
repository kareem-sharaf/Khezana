{{-- Public Item Price Section Partial --}}
{{-- Usage: @include('public.items._partials.detail.price', ['viewModel' => $viewModel]) --}}

<div class="khezana-item-price-section">
    @if ($viewModel->isFree)
        <div class="khezana-item-price khezana-item-price-free">
            <span class="khezana-price-label">{{ __('common.ui.free') }}</span>
        </div>
    @elseif ($viewModel->hasPrice)
        <div class="khezana-item-price">
            <span class="khezana-price-amount">{{ $viewModel->formattedDisplayPrice }}</span>
            <span class="khezana-price-currency">{{ __('common.ui.currency') }}</span>
            @if ($viewModel->showPriceUnit)
                <span class="khezana-price-unit">{{ __('common.ui.per_day') }}</span>
            @endif
        </div>
    @endif
    
    @if ($viewModel->hasDeposit)
        <div class="khezana-item-deposit">
            <span class="khezana-deposit-label">{{ __('common.ui.deposit') }}:</span>
            <span class="khezana-deposit-amount">{{ $viewModel->formattedDeposit }} {{ __('common.ui.currency') }}</span>
        </div>
    @endif
</div>
