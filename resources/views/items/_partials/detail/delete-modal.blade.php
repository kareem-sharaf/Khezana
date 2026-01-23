{{-- Delete Modal Partial --}}
{{-- Usage: @include('items._partials.detail.delete-modal', ['viewModel' => $viewModel]) --}}

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

            <form id="deleteForm" method="POST" action="{{ $viewModel->deleteUrl }}">
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
