<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = \App\Models\User::find($this->route('user') ?? $this->route('id'));
        return $this->user()?->can('update', $user) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userId = $this->route('user') ?? $this->route('id');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($userId)],
            'password' => ['sometimes', 'nullable', 'string', 'min:8', 'confirmed'],
            'status' => ['sometimes', 'nullable', 'string', Rule::in(['active', 'inactive', 'suspended'])],
            'roles' => ['sometimes', 'nullable', 'array'],
            'roles.*' => ['required', 'string', 'exists:roles,name'],
            // Profile fields
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'avatar' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.unique' => 'This email is already registered.',
            'phone.unique' => 'This phone number is already registered.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            'roles.exists' => 'One or more selected roles do not exist.',
        ];
    }
}
