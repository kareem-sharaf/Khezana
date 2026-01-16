<?php

namespace App\Http\Requests;

class UpdateUserProfileRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Users can update their own profile
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'avatar' => ['nullable', 'string', 'max:255'],
        ];
    }
}
