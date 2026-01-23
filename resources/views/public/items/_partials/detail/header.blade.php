{{-- Public Item Detail Header Partial --}}
{{-- Usage: @include('public.items._partials.detail.header', ['viewModel' => $viewModel]) --}}

<div class="khezana-item-header">
    <h1 class="khezana-item-detail-title">{{ $viewModel->title }}</h1>
    <span class="khezana-item-badge {{ $viewModel->operationTypeBadgeClass }}">
        {{ $viewModel->operationTypeLabel }}
    </span>
</div>
