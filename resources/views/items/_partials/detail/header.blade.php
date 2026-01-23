{{-- Item Detail Header Partial --}}
{{-- Usage: @include('items._partials.detail.header', ['viewModel' => $viewModel]) --}}

<div class="khezana-item-header">
    <h1 class="khezana-item-detail-title">{{ $viewModel->title }}</h1>
    <div style="display: flex; gap: var(--khezana-spacing-sm); flex-wrap: wrap;">
        <span class="khezana-item-badge {{ $viewModel->operationTypeBadgeClass }}">
            {{ $viewModel->operationTypeLabel }}
        </span>

        @if ($viewModel->approvalStatusClass)
            <span class="khezana-approval-badge {{ $viewModel->approvalStatusClass }}">
                {{ $viewModel->approvalStatusLabel }}
            </span>
        @endif
    </div>
</div>
