<?php

namespace App\Http\Requests;

use App\Models\Period;
use Illuminate\Foundation\Http\FormRequest;

class PeriodReopenRequest extends FormRequest
{
    public function authorize(): bool
    {
        $period = $this->route('period');
        return $period instanceof Period && ($this->user()?->can('reopen', $period) ?? false);
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }
}
