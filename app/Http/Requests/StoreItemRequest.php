<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * Form Request for storing a new item
 *
 * Handles validation for item creation with proper error messages
 * Phase 5.1: Input sanitization (strip_tags for title, allowed tags for description)
 */
class StoreItemRequest extends BaseFormRequest
{
    /** Allowed HTML tags for description (Phase 5.1) */
    private const DESCRIPTION_ALLOWED_TAGS = '<p><br><b><i><em><strong><ul><ol><li>';

    protected function prepareForValidation(): void
    {
        $merge = [];
        if ($this->has('title') && is_string($this->title)) {
            $merge['title'] = strip_tags($this->title);
        }
        if ($this->has('description') && is_string($this->description)) {
            $merge['description'] = strip_tags($this->description, self::DESCRIPTION_ALLOWED_TAGS);
        }
        if ($merge !== []) {
            $this->merge($merge);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'category_id' => [
                'required',
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    $category = \App\Models\Category::find($value);
                    if (!$category || !$category->is_active) {
                        $fail(__('items.messages.category_inactive', ['attribute' => __('items.fields.category')]));
                    }
                },
            ],
            'operation_type' => ['required', Rule::in(['sell', 'rent', 'donate'])],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'governorate' => [
                'nullable',
                'string',
                Rule::in([
                    'damascus',
                    'aleppo',
                    'homs',
                    'hama',
                    'latakia',
                    'tartus',
                    'daraa',
                    'sweida',
                    'hasakah',
                    'deir_ezzor',
                    'raqqa',
                    'idlib'
                ])
            ],
            'condition' => ['required', Rule::in(['new', 'used'])],
            'price' => ['nullable', 'numeric', 'min:0'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'is_available' => ['sometimes', 'boolean'],
            'attributes' => ['nullable', 'array'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,jpg,png', 'max:5120'], // Max 5MB per image
        ];

        // Dynamic validation based on operation_type
        $operationType = $this->input('operation_type');
        
        if ($operationType === 'sell') {
            $rules['price'] = ['required', 'numeric', 'min:0'];
            $rules['deposit_amount'] = ['nullable', 'numeric', 'min:0'];
        } elseif ($operationType === 'rent') {
            $rules['price'] = ['required', 'numeric', 'min:0'];
            $rules['deposit_amount'] = ['required', 'numeric', 'min:0'];
        } elseif ($operationType === 'donate') {
            $rules['price'] = ['nullable', 'numeric', 'min:0'];
            $rules['deposit_amount'] = ['nullable', 'numeric', 'min:0'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'category_id.required' => __('items.messages.category_required'),
            'category_id.exists' => __('common.messages.not_found'),
            'operation_type.required' => __('items.messages.operation_type_required'),
            'operation_type.in' => __('items.messages.operation_type_invalid'),
            'title.required' => __('items.messages.title_required'),
            'title.max' => __('items.messages.title_max'),
            'condition.required' => __('items.messages.condition_required'),
            'condition.in' => __('items.messages.condition_invalid'),
            'governorate.in' => __('items.messages.governorate_invalid'),
            'price.numeric' => __('items.messages.price_numeric'),
            'price.min' => __('items.messages.price_min'),
            'deposit_amount.required' => __('items.messages.deposit_required_for_rent'),
            'deposit_amount.numeric' => __('items.messages.deposit_numeric'),
            'deposit_amount.min' => __('items.messages.deposit_min'),
            'price.required' => __('items.messages.price_required'),
            'images.*.image' => __('items.messages.invalid_image_type'),
            'images.*.mimes' => __('items.messages.invalid_image_format'),
            'images.*.max' => __('items.messages.image_too_large'),
        ];
    }
}
