<?php

namespace App\Http\Requests;

use App\Models\Document;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentVersionStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        $document = $this->route('document');
        return $document instanceof Document && ($this->user()?->can('update', $document) ?? false);
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['draft', 'vigente', 'archivado'])],
            'content' => ['required', 'string'],
            'change_summary' => ['required', 'string', 'max:1000'],
            'change_reason' => ['required', 'string', 'max:1000'],
        ];
    }
}
