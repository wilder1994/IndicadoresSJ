<?php

namespace App\Http\Requests;

use App\Models\DashboardWeight;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class DashboardWeightUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $sample = DashboardWeight::query()->first();
        return $sample
            ? ($this->user()?->can('update', $sample) ?? false)
            : ($this->user()?->can('viewAny', DashboardWeight::class) ?? false);
    }

    public function rules(): array
    {
        return [
            'weights' => ['required', 'array', 'min:1'],
            'weights.*' => ['required', 'numeric', 'min:0', 'max:100'],
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }

    protected function passedValidation(): void
    {
        $weights = collect($this->validated('weights'))->map(fn ($value) => (float) $value);
        $sum = round($weights->sum(), 2);

        if ($sum !== 100.00) {
            throw ValidationException::withMessages([
                'weights' => 'La suma de pesos debe ser exactamente 100. Valor actual: '.$sum,
            ]);
        }
    }
}
