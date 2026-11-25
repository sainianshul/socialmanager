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
            'client_type' => [
                'nullable',
                Rule::requiredIf(fn () => (int) ($this->input('role', User::ROLE_USER)) === User::ROLE_USER),
                'string',
                Rule::in(array_keys(User::getClientTypeList())),
            ],
            'role' => ['nullable', 'integer', Rule::in([User::ROLE_MANAGER, User::ROLE_USER])],
            'status' => ['required', 'integer', Rule::in(array_keys(User::getStatusList()))],
            'can_schedule_posts' => ['nullable', 'boolean'],
            'can_use_festival_studio' => ['nullable', 'boolean'],
            'can_use_auto_dm' => ['nullable', 'boolean'],
            'can_use_ai' => ['nullable', 'boolean'],
            'ai_token_quota' => ['nullable', 'integer', 'min:0'],
            'can_manage_users' => ['nullable', 'boolean'],
            'can_manage_content' => ['nullable', 'boolean'],
            'can_manage_tickets' => ['nullable', 'boolean'],
            'can_access_system' => ['nullable', 'boolean'],
        ];
    }
}
