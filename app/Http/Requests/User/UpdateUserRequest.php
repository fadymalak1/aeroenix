<?php

namespace App\Http\Requests\User;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->exists('password') && $this->input('password') === '') {
            $this->merge(['password' => null]);
        }
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'password' => ['nullable', 'string', Password::defaults()],
            'role' => ['sometimes', 'required', Rule::enum(UserRole::class)],
            'status' => ['sometimes', 'required', Rule::enum(UserStatus::class)],
        ];
    }
}
