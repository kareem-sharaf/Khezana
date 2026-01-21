@extends('layouts.app')

@section('title', $requestModel->title . ' - ' . config('app.name'))

@section('content')
<div class="khezana-container khezana-request-show">
    <div class="khezana-breadcrumb">
        <a href="{{ route('requests.index') }}" class="khezana-link">{{ __('common.ui.my_requests_page') }}</a>
        <span class="khezana-breadcrumb-sep">/</span>
        <span>{{ $requestModel->title }}</span>
    </div>

    <div class="khezana-card">
        <div class="khezana-card-header">
            <div>
                <h1 class="khezana-page-title">{{ $requestModel->title }}</h1>
                <p class="khezana-text-muted">{{ $requestModel->category?->name ?? __('common.ui.unknown') }}</p>
            </div>
            <div class="khezana-badges">
                <span class="khezana-badge khezana-badge-primary">
                    {{ $requestModel->status->label() }}
                </span>
            </div>
            @if ($requestModel->status->value == 'open')
                <p class="khezana-status-explanation" style="margin-top: var(--khezana-spacing-xs);">
                    {{ __('requests.detail.status_explanation_open') }}
                </p>
            @elseif($requestModel->status->value == 'fulfilled')
                <p class="khezana-status-explanation" style="margin-top: var(--khezana-spacing-xs);">
                    {{ __('requests.detail.status_explanation_fulfilled') }}
                </p>
            @else
                <p class="khezana-status-explanation" style="margin-top: var(--khezana-spacing-xs);">
                    {{ __('requests.detail.status_explanation_closed') }}
                </p>
            @endif
        </div>

        <div class="khezana-card-body">
            <h3 class="khezana-section-title-small">{{ __('common.ui.description') }}</h3>
            <p class="khezana-text-body">{{ $requestModel->description ?: __('requests.messages.no_description') }}</p>

            @if($requestModel->itemAttributes && $requestModel->itemAttributes->count())
                <div class="khezana-attributes-grid">
                    @foreach($requestModel->itemAttributes as $attr)
                        <div class="khezana-attribute-item">
                            <div class="khezana-attribute-name">{{ $attr->attribute?->name ?? __('items.fields.attributes') }}</div>
                            <div class="khezana-attribute-value">{{ $attr->value }}</div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Next Steps Guide -->
            <div class="khezana-next-steps" style="margin-top: var(--khezana-spacing-xl);">
                <h3 class="khezana-section-title-small">{{ __('requests.detail.next_steps_title') }}</h3>
                <div class="khezana-next-steps-content">
                    @if ($requestModel->status->value == 'open')
                        <div class="khezana-next-step-item">
                            <div class="khezana-next-step-icon">✅</div>
                            <div class="khezana-next-step-text">
                                <p class="khezana-next-step-main">{{ __('requests.detail.next_steps_open') }}</p>
                                <p class="khezana-next-step-hint">{{ __('requests.detail.next_steps_open_hint') }}</p>
                            </div>
                        </div>
                        @if($requestModel->offers->count() == 0)
                            <div class="khezana-next-step-item">
                                <div class="khezana-next-step-icon">⏳</div>
                                <div class="khezana-next-step-text">
                                    <p class="khezana-next-step-main">{{ __('requests.detail.offers_coming') }}</p>
                                </div>
                            </div>
                        @else
                            <div class="khezana-next-step-item">
                                <div class="khezana-next-step-icon">📋</div>
                                <div class="khezana-next-step-text">
                                    <p class="khezana-next-step-main">{{ __('requests.detail.review_offers') }}</p>
                                </div>
                            </div>
                            <div class="khezana-next-step-item">
                                <div class="khezana-next-step-icon">💬</div>
                                <div class="khezana-next-step-text">
                                    <p class="khezana-next-step-main">{{ __('requests.detail.contact_offerer') }}</p>
                                </div>
                            </div>
                        @endif
                    @elseif($requestModel->status->value == 'fulfilled')
                        <div class="khezana-next-step-item">
                            <div class="khezana-next-step-icon">🎉</div>
                            <div class="khezana-next-step-text">
                                <p class="khezana-next-step-main">{{ __('requests.detail.next_steps_fulfilled') }}</p>
                            </div>
                        </div>
                    @else
                        <div class="khezana-next-step-item">
                            <div class="khezana-next-step-icon">🔒</div>
                            <div class="khezana-next-step-text">
                                <p class="khezana-next-step-main">{{ __('requests.detail.next_steps_closed') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="khezana-card-footer">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--khezana-spacing-md);">
                <span class="khezana-text-muted">
                    {{ __('common.ui.published') }}: {{ $requestModel->created_at?->format('Y-m-d H:i') }}
                </span>

                <div style="display: flex; gap: var(--khezana-spacing-sm); flex-wrap: wrap;">
                    @if (!$requestModel->isClosed() && !$requestModel->isFulfilled())
                        <a href="{{ route('requests.edit', $requestModel) }}" class="khezana-btn khezana-btn-secondary">
                            {{ __('common.actions.edit') }}
                        </a>
                    @endif

                    @php
                        $deletionService = app(\App\Services\RequestDeletionService::class);
                        $canDelete = $deletionService->canUserDelete(auth()->user(), $requestModel);
                        $blockReason = $deletionService->getDeletionBlockReason(auth()->user(), $requestModel);
                    @endphp

                    @if ($canDelete || auth()->user()->hasAnyRole(['admin', 'super_admin']))
                        <button type="button"
                            class="khezana-btn khezana-btn-delete"
                            onclick="openRequestDeleteModal({{ $requestModel->id }}, '{{ addslashes($requestModel->title) }}', {{ auth()->user()->hasAnyRole(['admin', 'super_admin']) ? 'true' : 'false' }}, {{ auth()->user()->hasRole('super_admin') ? 'true' : 'false' }})">
                            {{ __('common.actions.delete') }}
                        </button>
                    @else
                        <button type="button"
                            class="khezana-btn khezana-btn-delete khezana-btn-disabled"
                            disabled
                            title="{{ $blockReason }}">
                            {{ __('common.actions.delete') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal for Requests -->
<div id="requestDeleteModal" class="khezana-delete-modal" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="requestDeleteModalTitle">
    <div class="khezana-delete-modal-overlay" onclick="closeRequestDeleteModal()"></div>
    <div class="khezana-delete-modal-content">
        <div class="khezana-delete-modal-header">
            <h3 id="requestDeleteModalTitle" class="khezana-delete-modal-title">{{ __('requests.deletion.title') }}</h3>
            <button type="button" class="khezana-delete-modal-close" onclick="closeRequestDeleteModal()" aria-label="{{ __('common.ui.close') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <div class="khezana-delete-modal-body">
            <p class="khezana-delete-modal-message" id="requestDeleteModalMessage">
                {{ __('requests.deletion.confirmation') }}
            </p>

            <form id="requestDeleteForm" method="POST" action="">
                @csrf
                @method('DELETE')

                <div class="khezana-delete-form-group" id="requestReasonGroup" style="display: none;">
                    <label for="requestDeleteReason" class="khezana-delete-form-label">
                        {{ __('requests.deletion.reason_label') }} <span class="khezana-required">*</span>
                    </label>
                    <textarea
                        id="requestDeleteReason"
                        name="reason"
                        class="khezana-delete-form-textarea"
                        rows="3"
                        placeholder="{{ __('requests.deletion.reason_placeholder') }}"
                        required></textarea>
                </div>

                <div class="khezana-delete-form-group" id="requestArchiveGroup">
                    <label class="khezana-delete-form-checkbox">
                        <input type="checkbox" name="archive" id="requestArchiveCheckbox" value="1">
                        <span>
                            <strong>{{ __('requests.deletion.archive_option') }}</strong>
                            <small>{{ __('requests.deletion.archive_hint') }}</small>
                        </span>
                    </label>
                </div>

                <div class="khezana-delete-form-group" id="requestHardDeleteGroup" style="display: none;">
                    <div class="khezana-delete-warning">
                        <strong>{{ __('requests.deletion.permanent_warning') }}</strong>
                        <p>{{ __('requests.deletion.permanent_description') }}</p>
                    </div>
                    <label for="requestDeleteConfirmation" class="khezana-delete-form-label">
                        {{ __('requests.deletion.confirmation_required') }}
                    </label>
                    <input
                        type="text"
                        id="requestDeleteConfirmation"
                        name="confirmation"
                        class="khezana-delete-form-input"
                        placeholder="{{ __('requests.deletion.confirmation_placeholder') }}"
                        autocomplete="off"
                        oninput="validateRequestDeleteConfirmation(this)">
                    <div id="requestDeleteConfirmationFeedback" class="khezana-delete-confirmation-feedback" style="display: none;"></div>
                </div>

                <div class="khezana-delete-modal-actions">
                    <button type="button" class="khezana-btn khezana-btn-secondary" onclick="closeRequestDeleteModal()">
                        {{ __('requests.deletion.cancel_button') }}
                    </button>
                    <button type="submit" class="khezana-btn khezana-btn-danger" id="requestDeleteSubmitBtn">
                        {{ __('requests.deletion.delete_button') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openRequestDeleteModal(requestId, requestTitle, isAdmin, isSuperAdmin) {
        const modal = document.getElementById('requestDeleteModal');
        const form = document.getElementById('requestDeleteForm');
        const reasonGroup = document.getElementById('requestReasonGroup');
        const hardDeleteGroup = document.getElementById('requestHardDeleteGroup');
        const archiveGroup = document.getElementById('requestArchiveGroup');
        const message = document.getElementById('requestDeleteModalMessage');
        const submitBtn = document.getElementById('requestDeleteSubmitBtn');
        const archiveCheckbox = document.getElementById('requestArchiveCheckbox');

        // Set form action
        form.action = '{{ route('requests.destroy', ':id') }}'.replace(':id', requestId);

        // Show reason field for admin
        if (isAdmin) {
            reasonGroup.style.display = 'block';
            reasonGroup.querySelector('#requestDeleteReason').required = true;
        } else {
            reasonGroup.style.display = 'none';
            reasonGroup.querySelector('#requestDeleteReason').required = false;
        }

        // Hide hard delete initially
        hardDeleteGroup.style.display = 'none';
        archiveGroup.style.display = 'block';

        // Update message
        message.textContent = '{{ __('requests.deletion.confirmation') }}';

        // Update submit button
        submitBtn.textContent = '{{ __('requests.deletion.delete_button') }}';

        // Enable submit button for soft delete (confirmation not required)
        if (submitBtn && confirmationInput) {
            submitBtn.disabled = false;
        }

        // Handle archive checkbox change
        archiveCheckbox.onchange = function() {
            if (this.checked) {
                submitBtn.textContent = '{{ __('requests.deletion.archive_button') }}';
            } else {
                submitBtn.textContent = '{{ __('requests.deletion.delete_button') }}';
            }
        };

        // Show modal
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

        function closeRequestDeleteModal() {
            const modal = document.getElementById('requestDeleteModal');
            modal.style.display = 'none';
            document.body.style.overflow = '';

            // Reset form
            document.getElementById('requestDeleteForm').reset();
            document.getElementById('requestDeleteReason').required = false;

            // Reset confirmation input
            const confirmationInput = document.getElementById('requestDeleteConfirmation');
            if (confirmationInput) {
                confirmationInput.value = '';
                confirmationInput.removeAttribute('required');
                confirmationInput.classList.remove('khezana-input-valid', 'khezana-input-invalid');
                const feedback = document.getElementById('requestDeleteConfirmationFeedback');
                if (feedback) {
                    feedback.style.display = 'none';
                }
                // Reset button state (hard delete is hidden, so enable button)
                const submitBtn = document.getElementById('requestDeleteSubmitBtn');
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
            }
        }

        function validateRequestDeleteConfirmation(input) {
            const value = input.value.trim();
            const feedback = document.getElementById('requestDeleteConfirmationFeedback');
            const submitBtn = document.getElementById('requestDeleteSubmitBtn');
            const requiredValue = 'DELETE';

            // Remove previous classes
            input.classList.remove('khezana-input-valid', 'khezana-input-invalid');

            if (!feedback) return;

            if (value === '') {
                feedback.style.display = 'none';
                input.classList.remove('khezana-input-valid', 'khezana-input-invalid');
                // Check if hard delete group is visible (confirmation required)
                const hardDeleteGroup = document.getElementById('requestHardDeleteGroup');
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
                const remaining = requiredValue.length - value.length;
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

        // Validate on form submit
        document.addEventListener('DOMContentLoaded', function() {
            const requestDeleteForm = document.getElementById('requestDeleteForm');
            if (requestDeleteForm) {
                requestDeleteForm.addEventListener('submit', function(e) {
                    const confirmationInput = document.getElementById('requestDeleteConfirmation');
                    const hardDeleteGroup = document.getElementById('requestHardDeleteGroup');

                    // Check if hard delete is visible and required
                    if (confirmationInput && hardDeleteGroup && hardDeleteGroup.style.display !== 'none') {
                        if (confirmationInput.value.trim() !== 'DELETE') {
                            e.preventDefault();
                            confirmationInput.focus();
                            validateRequestDeleteConfirmation(confirmationInput);
                            return false;
                        }
                    }
                });
            }
        });
    </script>
@endsection
