<?php

namespace App\Http\Requests\Admin\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user') instanceof User ? $this->route('user')->id : $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:6'],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($userId)],
            'client_type' => ['required', 'string', Rule::in(array_keys(User::getClientTypeList()))],
            'role' => ['nullable', 'integer', Rule::in(array_keys(User::getRoleList()))],
            'status' => ['required', 'integer', Rule::in(array_keys(User::getStatusList()))],
            'can_schedule_posts' => ['nullable', 'boolean'],
            'can_use_festival_studio' => ['nullable', 'boolean'],
            'can_use_auto_dm' => ['nullable', 'boolean'],
            'can_use_ai' => ['nullable', 'boolean'],
            'ai_token_quota' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
