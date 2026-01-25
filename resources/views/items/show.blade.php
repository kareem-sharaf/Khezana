@extends('layouts.app')

@section('title', $viewModel->title . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-item-detail-page">
        <div class="khezana-container">
            @include('items._partials.detail.breadcrumb', ['viewModel' => $viewModel])

            <div class="khezana-item-detail-layout">
                {{-- Use enhanced images gallery like public page --}}
                @include('public.items._partials.detail.images-enhanced', ['viewModel' => $viewModel])

                <div class="khezana-item-details">
                    @include('items._partials.detail.header', ['viewModel' => $viewModel])
                    @include('items._partials.detail.price', ['viewModel' => $viewModel])
                    @include('items._partials.detail.meta', ['viewModel' => $viewModel])
                    @include('items._partials.detail.attributes', ['viewModel' => $viewModel])
                    @include('items._partials.detail.description', ['viewModel' => $viewModel])
                    @include('items._partials.detail.approval-info', ['viewModel' => $viewModel])
                    @include('items._partials.detail.actions', ['viewModel' => $viewModel])
                    @include('items._partials.detail.additional-info', ['viewModel' => $viewModel])
                </div>
            </div>
        </div>
    </div>

    @include('items._partials.detail.delete-modal', ['viewModel' => $viewModel])
    {{-- Include image modal for zoom functionality --}}
    @include('public.items._partials.detail.image-modal', ['viewModel' => $viewModel])
@endsection

@push('scripts')
    {{-- Delete modal scripts (without changeMainImage to avoid conflict) --}}
    <script>
        function openDeleteModal(itemId, itemTitle, isAdmin, isSuperAdmin) {
            const modal = document.getElementById('deleteModal');
            const form = document.getElementById('deleteForm');
            const reasonGroup = document.getElementById('reasonGroup');
            const hardDeleteGroup = document.getElementById('hardDeleteGroup');
            const archiveGroup = document.getElementById('archiveGroup');
            const message = document.getElementById('deleteModalMessage');
            const submitBtn = document.getElementById('deleteSubmitBtn');
            const archiveCheckbox = document.getElementById('archiveCheckbox');
            const confirmationInput = document.getElementById('deleteConfirmation');

            form.action = '{{ route('items.destroy', ':id') }}'.replace(':id', itemId);

            if (isAdmin) {
                reasonGroup.style.display = 'block';
                reasonGroup.querySelector('#deleteReason').required = true;
            } else {
                reasonGroup.style.display = 'none';
                reasonGroup.querySelector('#deleteReason').required = false;
            }

            hardDeleteGroup.style.display = 'none';
            archiveGroup.style.display = 'block';

            message.textContent = '{{ __('items.deletion.confirmation') }}'.replace(':title', itemTitle);

            submitBtn.textContent = '{{ __('items.deletion.delete_button') }}';
            submitBtn.classList.remove('khezana-btn-danger-permanent');

            if (submitBtn && confirmationInput) {
                submitBtn.disabled = false;
            }

            archiveCheckbox.onchange = function() {
                if (this.checked) {
                    submitBtn.textContent = '{{ __('items.deletion.archive_button') }}';
                } else {
                    submitBtn.textContent = '{{ __('items.deletion.delete_button') }}';
                }
            };

            if (confirmationInput) {
                confirmationInput.value = '';
                confirmationInput.removeAttribute('required');
                confirmationInput.classList.remove('khezana-input-valid', 'khezana-input-invalid');
                const feedback = document.getElementById('deleteConfirmationFeedback');
                if (feedback) {
                    feedback.style.display = 'none';
                }
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
            }

            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function validateDeleteConfirmation(input) {
            const value = input.value.trim();
            const feedback = document.getElementById('deleteConfirmationFeedback');
            const submitBtn = document.getElementById('deleteSubmitBtn');
            const requiredValue = 'DELETE';

            input.classList.remove('khezana-input-valid', 'khezana-input-invalid');

            if (!feedback) return;

            if (value === '') {
                feedback.style.display = 'none';
                input.classList.remove('khezana-input-valid', 'khezana-input-invalid');
                const hardDeleteGroup = document.getElementById('hardDeleteGroup');
                if (submitBtn && hardDeleteGroup && hardDeleteGroup.style.display !== 'none') {
                    submitBtn.disabled = true;
                }
                return;
            }

            feedback.style.display = 'block';

            if (value === requiredValue) {
                input.classList.add('khezana-input-valid');
                input.classList.remove('khezana-input-invalid');
                feedback.className = 'khezana-delete-confirmation-feedback khezana-feedback-valid';
                feedback.textContent = '✓ صحيح';
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
            } else {
                input.classList.add('khezana-input-invalid');
                input.classList.remove('khezana-input-valid');
                feedback.className = 'khezana-delete-confirmation-feedback khezana-feedback-invalid';
                if (value.length < requiredValue.length) {
                    feedback.textContent = 'اكتب ' + requiredValue.substring(value.length);
                } else {
                    feedback.textContent = 'القيمة غير صحيحة. يجب أن تكون: DELETE';
                }
                if (submitBtn) {
                    submitBtn.disabled = true;
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const deleteForm = document.getElementById('deleteForm');
            if (deleteForm) {
                deleteForm.addEventListener('submit', function(e) {
                    const confirmationInput = document.getElementById('deleteConfirmation');
                    const hardDeleteGroup = document.getElementById('hardDeleteGroup');

                    if (confirmationInput && hardDeleteGroup && hardDeleteGroup.style.display !== 'none') {
                        if (confirmationInput.value.trim() !== 'DELETE') {
                            e.preventDefault();
                            confirmationInput.focus();
                            validateDeleteConfirmation(confirmationInput);
                            return false;
                        }
                    }
                });
            }
        });

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.style.display = 'none';
            document.body.style.overflow = '';

            document.getElementById('deleteForm').reset();
            document.getElementById('deleteReason').required = false;
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const deleteModal = document.getElementById('deleteModal');
                if (deleteModal && deleteModal.style.display === 'flex') {
                    closeDeleteModal();
                }
            }
        });
    </script>
    
    {{-- Enhanced gallery scripts (loaded last to ensure proper initialization) --}}
    @include('public.items._partials.detail.scripts-enhanced', ['viewModel' => $viewModel])
@endpush
