<?php

namespace App\Http\Requests;

use App\Models\AnalysisSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AnalysisSettingUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $setting = AnalysisSetting::query()->first();
        return $setting
            ? ($this->user()?->can('update', $setting) ?? false)
            : ($this->user()?->can('viewAny', AnalysisSetting::class) ?? false);
    }

    public function rules(): array
    {
        return [
            'mode' => ['required', Rule::in([
                AnalysisSetting::MODE_RULES,
                AnalysisSetting::MODE_LOCAL,
                AnalysisSetting::MODE_OPENAI,
            ])],
            'rules_enabled' => ['required', 'boolean'],
            'local_endpoint_url' => ['nullable', 'url', 'max:1000'],
            'local_model' => ['nullable', 'string', 'max:255'],
            'local_timeout_ms' => ['required', 'integer', 'min:1000', 'max:120000'],
            'openai_model' => ['required', 'string', 'max:255'],
            'openai_timeout_ms' => ['required', 'integer', 'min:1000', 'max:120000'],
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }
}
