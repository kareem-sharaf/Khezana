<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * Form Request for filtering items
 * Performance fix #16: Move validation from Controller to Request
 */
class FilterItemsRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'operation_type' => ['nullable', Rule::in(['sell', 'rent', 'donate'])],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'condition' => ['nullable', Rule::in(['new', 'used'])],
            'price_min' => ['nullable', 'numeric', 'min:0'],
            'price_max' => ['nullable', 'numeric', 'min:0', 'gte:price_min'],
            'search' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', Rule::in(['price_asc', 'price_desc', 'title_asc', 'title_desc', 'created_at_desc', 'updated_at_desc'])],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->has('price_min') && $this->has('price_max')) {
                $priceMin = (float) $this->input('price_min', 0);
                $priceMax = (float) $this->input('price_max', 0);
                if ($priceMin > 0 && $priceMax > 0 && $priceMin > $priceMax) {
                    $validator->errors()->add('price_min', __('items.messages.price_min_max_error'));
                }
            }
        });
    }
}
