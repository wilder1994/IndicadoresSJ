<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', User::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_USUARIO])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'zone_ids' => ['required', 'array', 'min:1'],
            'zone_ids.*' => ['integer', 'exists:zones,id'],
        ];
    }
}
