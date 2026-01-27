<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Store User Request
 *
 * Validates user creation with role-based branch_id requirements:
 * - seller: MUST have branch_id
 * - admin/super_admin/user: MUST NOT have branch_id
 */
class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create_users');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended'])],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => [Rule::in(['super_admin', 'admin', 'seller', 'user'])],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            // Profile fields
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'avatar' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.required', ['attribute' => 'اسم المستخدم']),
            'email.required' => __('validation.required', ['attribute' => 'البريد الإلكتروني']),
            'email.unique' => __('validation.unique', ['attribute' => 'البريد الإلكتروني']),
            'phone.unique' => __('validation.unique', ['attribute' => 'رقم الهاتف']),
            'password.required' => __('validation.required', ['attribute' => 'كلمة المرور']),
            'password.confirmed' => __('validation.confirmed', ['attribute' => 'كلمة المرور']),
            'roles.required' => __('validation.required', ['attribute' => 'الأدوار']),
            'branch_id.exists' => __('validation.exists', ['attribute' => 'الفرع']),
        ];
    }

    /**
     * Configure the validator instance with custom validation logic.
     */
    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $roles = $this->input('roles', []);
            $branchId = $this->input('branch_id');

            $isSeller = in_array('seller', $roles);
            $isAdmin = in_array('admin', $roles) || in_array('super_admin', $roles);
            $isUser = in_array('user', $roles);

            // Seller must have branch_id
            if ($isSeller && empty($branchId)) {
                $validator->errors()->add(
                    'branch_id',
                    'البائع يجب أن يكون مرتبطاً بفرع'
                );
            }

            // Admin/Super Admin/User cannot have branch_id
            if (($isAdmin || $isUser) && !empty($branchId)) {
                $validator->errors()->add(
                    'branch_id',
                    sprintf(
                        'لا يمكن إسناد فرع لمستخدم ذو دور %s',
                        $isAdmin ? 'إداري' : 'عادي'
                    )
                );
            }
        });
    }
}
            'roles.exists' => 'One or more selected roles do not exist.',
        ];
    }
}
