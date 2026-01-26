{{-- Item Approval Info Partial --}}
{{-- Usage: @include('items._partials.detail.approval-info', ['viewModel' => $viewModel]) --}}

@if ($viewModel->isPending)
    <div class="khezana-info-box khezana-info-box-warning">
        <strong>â³ {{ __('common.ui.pending_review') }}:</strong>
        {{ __('common.ui.pending_review_message') }}
    </div>
@elseif ($viewModel->isRejected)
    <div class="khezana-info-box khezana-info-box-error">
        <strong>âŒ {{ __('common.ui.rejected') }}:</strong>
        {{ $viewModel->rejectionReason ?? __('common.ui.rejected_message') }}
    </div>
@elseif ($viewModel->isVerificationRequired)
    <div class="khezana-info-box khezana-info-box-warning">
        <strong>🔍 {{ __('approvals.statuses.verification_required') }}:</strong>
        {{ __('approvals.messages.verification_user_notice') }}
    </div>
@elseif ($viewModel->isApproved)
    <div class="khezana-info-box khezana-info-box-success">
        <strong>âœ… {{ __('common.ui.approved') }}:</strong>
        {{ __('common.ui.approved_message') }}
    </div>
@endif
