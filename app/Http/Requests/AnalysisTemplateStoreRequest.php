<?php

namespace App\Http\Requests;

use App\Models\AnalysisTemplate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AnalysisTemplateStoreRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $raw = (string) $this->input('sugerencias_accion_raw', '');
        $items = collect(preg_split('/\r\n|\r|\n/', $raw))
            ->map(fn (?string $line) => trim((string) $line))
            ->filter()
            ->values()
            ->all();

        $this->merge(['sugerencias_accion' => $items]);
    }

    public function authorize(): bool
    {
        return $this->user()?->can('create', AnalysisTemplate::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'indicator_id' => ['required', 'integer', 'exists:indicators,id', Rule::unique('analysis_templates', 'indicator_id')],
            'plantilla_cumple' => ['required', 'string'],
            'plantilla_no_cumple' => ['required', 'string'],
            'sugerencias_accion' => ['required', 'array', 'min:1'],
            'sugerencias_accion.*' => ['required', 'string'],
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }
}
