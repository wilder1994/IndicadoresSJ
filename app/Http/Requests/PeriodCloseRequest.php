<?php

namespace App\Http\Requests;

use App\Models\Period;
use Illuminate\Foundation\Http\FormRequest;

class PeriodCloseRequest extends FormRequest
{
    public function authorize(): bool
    {
        $period = $this->route('period');
        return $period instanceof Period && ($this->user()?->can('close', $period) ?? false);
    }

    public function rules(): array
    {
        return [
            'reason' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
