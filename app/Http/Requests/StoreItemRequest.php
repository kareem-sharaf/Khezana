<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * Form Request for storing a new item
 *
 * Handles validation for item creation with proper error messages
 */
class StoreItemRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
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
            'deposit_amount.numeric' => __('items.messages.deposit_numeric'),
            'deposit_amount.min' => __('items.messages.deposit_min'),
            'images.*.image' => __('items.messages.invalid_image_type'),
            'images.*.mimes' => __('items.messages.invalid_image_format'),
            'images.*.max' => __('items.messages.image_too_large'),
        ];
    }
}
