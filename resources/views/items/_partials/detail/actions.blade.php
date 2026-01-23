{{-- Item Actions Partial --}}
{{-- Usage: @include('items._partials.detail.actions', ['viewModel' => $viewModel]) --}}

@php
    $deletionService = app(\App\Services\ItemDeletionService::class);
    $item = \App\Models\Item::find($viewModel->itemId);
    $canDelete = $item ? $deletionService->canUserDelete(auth()->user(), $item) : false;
    $blockReason = $item ? $deletionService->getDeletionBlockReason(auth()->user(), $item) : '';
    $isAdmin = auth()->user()->hasAnyRole(['admin', 'super_admin']);
    $isSuperAdmin = auth()->user()->hasRole('super_admin');
    $isApproved = $item ? $item->isApproved() : false;
    $canEdit = $item ? auth()->user()->can('update', $item) : false;
@endphp

<div class="khezana-item-actions">
    @if ($viewModel->canSubmitForApproval)
        <form method="POST" action="{{ $viewModel->submitForApprovalUrl }}" style="display: inline;">
            @csrf
            <button type="submit" class="khezana-btn khezana-btn-primary">
                {{ __('common.ui.submit_for_approval') }}
            </button>
        </form>
    @endif

    @if ($canEdit)
        <a href="{{ $viewModel->editUrl }}" class="khezana-btn khezana-btn-secondary">
            {{ __('common.ui.edit') }}
        </a>
    @elseif($viewModel->canEdit && $isApproved)
        <button 
            type="button"
            class="khezana-btn khezana-btn-secondary khezana-btn-disabled"
            disabled
            title="{{ __('items.deletion.cannot_edit_approved') }}">
            {{ __('common.ui.edit') }}
        </button>
    @endif

    @if ($viewModel->canDelete)
        {{-- Hide delete button for regular users if item is approved --}}
        @if (!$isApproved || $isAdmin)
            @if ($canDelete || $isAdmin)
                <button 
                    type="button"
                    class="khezana-btn khezana-btn-delete"
                    onclick="openDeleteModal({{ $viewModel->itemId }}, '{{ addslashes($viewModel->title) }}', {{ $isAdmin ? 'true' : 'false' }}, {{ $isSuperAdmin ? 'true' : 'false' }})"
                    @if(!$canDelete && !$isAdmin)
                        disabled
                        title="{{ $blockReason }}"
                    @endif>
                    {{ __('common.actions.delete') }}
                </button>
            @else
                <button 
                    type="button"
                    class="khezana-btn khezana-btn-delete khezana-btn-disabled"
                    disabled
                    title="{{ $blockReason }}">
                    {{ __('common.actions.delete') }}
                </button>
            @endif
        @endif
    @endif
</div>

