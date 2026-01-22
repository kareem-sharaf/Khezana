<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * Form Request for updating an existing item
 * 
 * Handles validation for item updates with proper error messages
 */
class UpdateItemRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'exists:categories,id'],
            'operation_type' => ['sometimes', Rule::in(['sell', 'rent', 'donate'])],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'governorate' => [
                'nullable',
                'string',
                Rule::in([
                    'damascus', 'aleppo', 'homs', 'hama', 'latakia', 'tartus',
                    'daraa', 'sweida', 'hasakah', 'deir_ezzor', 'raqqa', 'idlib'
                ])
            ],
            'condition' => ['sometimes', Rule::in(['new', 'used'])],
            'price' => ['nullable', 'numeric', 'min:0'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'is_available' => ['sometimes', 'boolean'],
            'attributes' => ['nullable', 'array'],
            'images' => ['nullable', 'array'],
            'images.*' => ['string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'category_id.exists' => __('common.messages.not_found'),
            'operation_type.in' => __('items.messages.operation_type_invalid'),
            'title.max' => __('items.messages.title_max'),
            'condition.in' => __('items.messages.condition_invalid'),
            'governorate.in' => __('items.messages.governorate_invalid'),
            'price.numeric' => __('items.messages.price_numeric'),
            'price.min' => __('items.messages.price_min'),
            'deposit_amount.numeric' => __('items.messages.deposit_numeric'),
            'deposit_amount.min' => __('items.messages.deposit_min'),
        ];
    }
}
