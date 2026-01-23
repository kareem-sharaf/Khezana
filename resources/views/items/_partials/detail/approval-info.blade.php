{{-- Item Approval Info Partial --}}
{{-- Usage: @include('items._partials.detail.approval-info', ['viewModel' => $viewModel]) --}}

@if ($viewModel->isPending)
    <div class="khezana-info-box khezana-info-box-warning">
        <strong>⏳ {{ __('common.ui.pending_review') }}:</strong>
        {{ __('common.ui.pending_review_message') }}
    </div>
@elseif ($viewModel->isRejected)
    <div class="khezana-info-box khezana-info-box-error">
        <strong>❌ {{ __('common.ui.rejected') }}:</strong>
        {{ $viewModel->rejectionReason ?? __('common.ui.rejected_message') }}
    </div>
@elseif ($viewModel->isApproved)
    <div class="khezana-info-box khezana-info-box-success">
        <strong>✅ {{ __('common.ui.approved') }}:</strong>
        {{ __('common.ui.approved_message') }}
    </div>
@endif
