<?php

namespace App\Http\Requests;

use App\Models\Zone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ZoneUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $zone = $this->route('zone');
        return $zone instanceof Zone && ($this->user()?->can('update', $zone) ?? false);
    }

    public function rules(): array
    {
        /** @var Zone $zone */
        $zone = $this->route('zone');

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('zones', 'code')->ignore($zone->id)],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
