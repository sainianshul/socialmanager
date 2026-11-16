<?php

namespace App\Http\Requests\Admin\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['nullable', 'string', 'min:6'],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone')],
            'role' => ['nullable', 'integer', Rule::in([User::ROLE_MANAGER, User::ROLE_USER])],
            'status' => ['required', 'integer', Rule::in(array_keys(User::getStatusList()))],
        ];
    }
}
