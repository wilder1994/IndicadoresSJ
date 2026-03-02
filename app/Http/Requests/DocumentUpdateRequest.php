<?php

namespace App\Http\Requests;

use App\Models\Document;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $document = $this->route('document');
        return $document instanceof Document && ($this->user()?->can('update', $document) ?? false);
    }

    public function rules(): array
    {
        /** @var Document $document */
        $document = $this->route('document');

        return [
            'slug' => ['required', 'string', 'max:255', Rule::unique('documents', 'slug')->ignore($document->id)],
            'title' => ['required', 'string', 'max:255'],
            'scope' => ['required', Rule::in(['system', 'indicator', 'dashboard'])],
            'indicator_id' => ['nullable', 'integer', 'exists:indicators,id'],
            'is_active' => ['required', 'boolean'],
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }
}
