<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $targetUser = $this->route('user');
        return $targetUser instanceof User && ($this->user()?->can('update', $targetUser) ?? false);
    }

    public function rules(): array
    {
        /** @var User $targetUser */
        $targetUser = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($targetUser->id)],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_USUARIO])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'zone_ids' => ['required', 'array', 'min:1'],
            'zone_ids.*' => ['integer', 'exists:zones,id'],
        ];
    }
}
