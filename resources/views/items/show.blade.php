@extends('layouts.app')

@section('title', $item->title . ' - ' . config('app.name'))

@section('content')
    <div class="khezana-item-detail-page">
        <div class="khezana-container">
            <!-- Breadcrumb -->
            <nav class="khezana-breadcrumb">
                <a href="{{ route('home') }}">{{ __('common.ui.home') }}</a>
                <span>/</span>
                <a href="{{ route('items.index') }}">{{ __('common.ui.my_items_page') }}</a>
                <span>/</span>
                <span>{{ $item->title }}</span>
            </nav>

            <div class="khezana-item-detail-layout">
                <!-- Images Section -->
                <div class="khezana-item-images">
                    @if ($item->images->count() > 0)
                        <!-- Main Image -->
                        <div class="khezana-item-main-image">
                            @php
                                $primaryImage =
                                    $item->images->where('is_primary', true)->first() ?? $item->images->first();
                            @endphp
                            <img id="mainImage" src="{{ asset('storage/' . $primaryImage->path) }}" alt="{{ $item->title }}"
                                class="khezana-main-img" loading="eager">
                        </div>

                        <!-- Thumbnails (if more than one image) -->
                        @if ($item->images->count() > 1)
                            <div class="khezana-item-thumbnails">
                                @foreach ($item->images as $image)
                                    <button type="button" class="khezana-thumbnail {{ $loop->first ? 'active' : '' }}"
                                        onclick="changeMainImage('{{ asset('storage/' . $image->path) }}', this)">
                                        <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $item->title }}"
                                            loading="lazy">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="khezana-item-main-image khezana-no-image">
                            <div class="khezana-no-image-placeholder">
                                {{ __('common.ui.no_image') }}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Details Section -->
                <div class="khezana-item-details">
                    <!-- Header -->
                    <div class="khezana-item-header">
                        <h1 class="khezana-item-detail-title">{{ $item->title }}</h1>
                        <div style="display: flex; gap: var(--khezana-spacing-sm); flex-wrap: wrap;">
                            <span class="khezana-item-badge khezana-item-badge-{{ $item->operation_type->value }}">
                                {{ __('items.operation_types.' . $item->operation_type->value) }}
                            </span>

                            @if ($item->approvalRelation)
                                @php
                                    $status = $item->approvalRelation->status;
                                    $statusClass = match ($status->value) {
                                        'approved' => 'khezana-approval-badge-approved',
                                        'pending' => 'khezana-approval-badge-pending',
                                        'rejected' => 'khezana-approval-badge-rejected',
                                        'archived' => 'khezana-approval-badge-archived',
                                        default => 'khezana-approval-badge-pending',
                                    };
                                @endphp
                                <span class="khezana-approval-badge {{ $statusClass }}">
                                    {{ $status->label() }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="khezana-item-price-section">
                        @if ($item->operation_type->value == 'donate')
                            <div class="khezana-item-price khezana-item-price-free">
                                <span class="khezana-price-label">{{ __('common.ui.free') }}</span>
                            </div>
                        @elseif($item->price)
                            <div class="khezana-item-price">
                                <span class="khezana-price-amount">{{ number_format($item->price, 0) }}</span>
                                <span class="khezana-price-currency">{{ __('common.ui.currency') }}</span>
                                @if ($item->operation_type->value == 'rent')
                                    <span class="khezana-price-unit">{{ __('common.ui.per_day') }}</span>
                                @endif
                            </div>
                            @if ($item->operation_type->value == 'rent' && $item->deposit_amount)
                                <div class="khezana-item-deposit">
                                    <span class="khezana-deposit-label">{{ __('common.ui.deposit') }}:</span>
                                    <span class="khezana-deposit-amount">{{ number_format($item->deposit_amount, 0) }}
                                        {{ __('common.ui.currency') }}</span>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Category -->
                    @if ($item->category)
                        <div class="khezana-item-meta">
                            <span class="khezana-meta-label">{{ __('common.ui.category') }}:</span>
                            <span class="khezana-meta-value">{{ $item->category->name }}</span>
                        </div>
                    @endif

                    <!-- Availability -->
                    <div class="khezana-item-meta">
                        <span class="khezana-meta-label">{{ __('common.ui.status') }}:</span>
                        <span class="khezana-meta-value">
                            {{ $item->is_available ? __('common.ui.available') : __('common.ui.unavailable') }}
                        </span>
                    </div>

                    <!-- Attributes -->
                    @if ($item->itemAttributes->count() > 0)
                        <div class="khezana-item-attributes">
                            <h3 class="khezana-section-title-small">{{ __('items.fields.attributes') }}</h3>
                            <div class="khezana-attributes-grid">
                                @foreach ($item->itemAttributes as $itemAttribute)
                                    <div class="khezana-attribute-item">
                                        <span class="khezana-attribute-name">{{ $itemAttribute->attribute->name }}:</span>
                                        <span class="khezana-attribute-value">{{ $itemAttribute->value }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Description -->
                    @if ($item->description)
                        <div class="khezana-item-description">
                            <h3 class="khezana-section-title-small">{{ __('items.fields.description') }}</h3>
                            <p class="khezana-description-text">{{ $item->description }}</p>
                        </div>
                    @endif

                    <!-- Approval Status Info -->
                    @if ($item->approvalRelation)
                        <div class="khezana-approval-info">
                            @if ($item->approvalRelation->status->value == 'pending')
                                <div class="khezana-info-box khezana-info-box-warning">
                                    <strong>⏳ {{ __('common.ui.pending_review') }}:</strong>
                                    {{ __('common.ui.pending_review_message') }}
                                </div>
                            @elseif($item->approvalRelation->status->value == 'rejected')
                                <div class="khezana-info-box khezana-info-box-error">
                                    <strong>❌ {{ __('common.ui.rejected') }}:</strong>
                                    @if ($item->approvalRelation->rejection_reason)
                                        {{ $item->approvalRelation->rejection_reason }}
                                    @else
                                        {{ __('common.ui.rejected_message') }}
                                    @endif
                                </div>
                            @elseif($item->approvalRelation->status->value == 'approved')
                                <div class="khezana-info-box khezana-info-box-success">
                                    <strong>✅ {{ __('common.ui.approved') }}:</strong>
                                    {{ __('common.ui.approved_message') }}
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="khezana-item-actions">
                        @if (!$item->isPending() && !$item->isApproved())
                            <form method="POST" action="{{ route('items.submit-for-approval', $item) }}"
                                style="display: inline;">
                                @csrf
                                <button type="submit" class="khezana-btn khezana-btn-primary">
                                    {{ __('common.ui.submit_for_approval') }}
                                </button>
                            </form>
                        @endif

                        @if (!$item->isPending())
                            <a href="{{ route('items.edit', $item) }}" class="khezana-btn khezana-btn-secondary">
                                {{ __('common.ui.edit') }}
                            </a>
                        @endif

                        @php
                            $deletionService = app(\App\Services\ItemDeletionService::class);
                            $canDelete = $deletionService->canUserDelete(auth()->user(), $item);
                            $blockReason = $deletionService->getDeletionBlockReason(auth()->user(), $item);
                        @endphp

                        @if ($canDelete || auth()->user()->hasAnyRole(['admin', 'super_admin']))
                            <button type="button"
                                class="khezana-btn khezana-btn-delete"
                                onclick="openDeleteModal({{ $item->id }}, '{{ addslashes($item->title) }}', {{ auth()->user()->hasAnyRole(['admin', 'super_admin']) ? 'true' : 'false' }}, {{ auth()->user()->hasRole('super_admin') ? 'true' : 'false' }})"
                                @if(!$canDelete && !auth()->user()->hasAnyRole(['admin', 'super_admin']))
                                    disabled
                                    title="{{ $blockReason }}"
                                @endif>
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

                    <!-- Additional Info -->
                    <div class="khezana-item-additional-info">
                        <div class="khezana-info-item">
                            <span class="khezana-info-icon">📅</span>
                            <span class="khezana-info-text">{{ __('common.ui.published') }}
                                {{ $item->created_at->diffForHumans() }}</span>
                        </div>
                        @if ($item->updated_at != $item->created_at)
                            <div class="khezana-info-item">
                                <span class="khezana-info-icon">🔄</span>
                                <span class="khezana-info-text">{{ __('common.ui.last_updated') }}
                                    {{ $item->updated_at->diffForHumans() }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="khezana-delete-modal" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
        <div class="khezana-delete-modal-overlay" onclick="closeDeleteModal()"></div>
        <div class="khezana-delete-modal-content">
            <div class="khezana-delete-modal-header">
                <h3 id="deleteModalTitle" class="khezana-delete-modal-title">{{ __('items.deletion.title') }}</h3>
                <button type="button" class="khezana-delete-modal-close" onclick="closeDeleteModal()" aria-label="{{ __('common.ui.close') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            <div class="khezana-delete-modal-body">
                <p class="khezana-delete-modal-message" id="deleteModalMessage">
                    {{ __('items.deletion.confirmation') }}
                </p>

                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')

                    <div class="khezana-delete-form-group" id="reasonGroup" style="display: none;">
                        <label for="deleteReason" class="khezana-delete-form-label">
                            {{ __('items.deletion.reason_label') }} <span class="khezana-required">*</span>
                        </label>
                        <textarea
                            id="deleteReason"
                            name="reason"
                            class="khezana-delete-form-textarea"
                            rows="3"
                            placeholder="{{ __('items.deletion.reason_placeholder') }}"
                            required></textarea>
                    </div>

                    <div class="khezana-delete-form-group" id="archiveGroup">
                        <label class="khezana-delete-form-checkbox">
                            <input type="checkbox" name="archive" id="archiveCheckbox" value="1">
                            <span>
                                <strong>{{ __('items.deletion.archive_option') }}</strong>
                                <small>{{ __('items.deletion.archive_hint') }}</small>
                            </span>
                        </label>
                    </div>

                    <div class="khezana-delete-form-group" id="hardDeleteGroup" style="display: none;">
                        <div class="khezana-delete-warning">
                            <strong>{{ __('items.deletion.permanent_warning') }}</strong>
                            <p>{{ __('items.deletion.permanent_description') }}</p>
                        </div>
                        <label for="deleteConfirmation" class="khezana-delete-form-label">
                            {{ __('items.deletion.confirmation_required') }}
                        </label>
                        <input
                            type="text"
                            id="deleteConfirmation"
                            name="confirmation"
                            class="khezana-delete-form-input"
                            placeholder="{{ __('items.deletion.confirmation_placeholder') }}"
                            autocomplete="off"
                            oninput="validateDeleteConfirmation(this)">
                        <div id="deleteConfirmationFeedback" class="khezana-delete-confirmation-feedback" style="display: none;"></div>
                    </div>

                    <div class="khezana-delete-modal-actions">
                        <button type="button" class="khezana-btn khezana-btn-secondary" onclick="closeDeleteModal()">
                            {{ __('items.deletion.cancel_button') }}
                        </button>
                        <button type="submit" class="khezana-btn khezana-btn-danger" id="deleteSubmitBtn">
                            {{ __('items.deletion.delete_button') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Simple JavaScript for Image Gallery and Delete Modal -->
    <script>
        function changeMainImage(src, element) {
            document.getElementById('mainImage').src = src;

            // Update active thumbnail
            document.querySelectorAll('.khezana-thumbnail').forEach(thumb => {
                thumb.classList.remove('active');
            });
            element.classList.add('active');
        }

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

            // Set form action
            form.action = '{{ route('items.destroy', ':id') }}'.replace(':id', itemId);

            // Show reason field for admin
            if (isAdmin) {
                reasonGroup.style.display = 'block';
                reasonGroup.querySelector('#deleteReason').required = true;
            } else {
                reasonGroup.style.display = 'none';
                reasonGroup.querySelector('#deleteReason').required = false;
            }

            // Hide hard delete initially
            hardDeleteGroup.style.display = 'none';
            archiveGroup.style.display = 'block';

            // Update message
            message.textContent = '{{ __('items.deletion.confirmation') }}'.replace(':title', itemTitle);

            // Update submit button
            submitBtn.textContent = '{{ __('items.deletion.delete_button') }}';
            submitBtn.classList.remove('khezana-btn-danger-permanent');

            // Enable submit button for soft delete (confirmation not required)
            if (submitBtn && confirmationInput) {
                submitBtn.disabled = false;
            }

            // Handle archive checkbox change
            archiveCheckbox.onchange = function() {
                if (this.checked) {
                    submitBtn.textContent = '{{ __('items.deletion.archive_button') }}';
                } else {
                    submitBtn.textContent = '{{ __('items.deletion.delete_button') }}';
                }
            };

            // Reset confirmation input
            if (confirmationInput) {
                confirmationInput.value = '';
                confirmationInput.removeAttribute('required');
                confirmationInput.classList.remove('khezana-input-valid', 'khezana-input-invalid');
                const feedback = document.getElementById('deleteConfirmationFeedback');
                if (feedback) {
                    feedback.style.display = 'none';
                }
                // Reset button state (hard delete is hidden, so enable button)
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
            }

            // Show modal
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function validateDeleteConfirmation(input) {
            const value = input.value.trim();
            const feedback = document.getElementById('deleteConfirmationFeedback');
            const submitBtn = document.getElementById('deleteSubmitBtn');
            const requiredValue = 'DELETE';

            // Remove previous classes
            input.classList.remove('khezana-input-valid', 'khezana-input-invalid');

            if (!feedback) return;

            if (value === '') {
                feedback.style.display = 'none';
                input.classList.remove('khezana-input-valid', 'khezana-input-invalid');
                // Check if hard delete group is visible (confirmation required)
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
            const deleteForm = document.getElementById('deleteForm');
            if (deleteForm) {
                deleteForm.addEventListener('submit', function(e) {
                    const confirmationInput = document.getElementById('deleteConfirmation');
                    const hardDeleteGroup = document.getElementById('hardDeleteGroup');

                    // Check if hard delete is visible and required
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

            // Reset form
            document.getElementById('deleteForm').reset();
            document.getElementById('deleteReason').required = false;
        }

        // Close on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });
    </script>
@endsection
