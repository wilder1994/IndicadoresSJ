<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalysisTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'indicator_id',
        'plantilla_cumple',
        'plantilla_no_cumple',
        'sugerencias_accion',
        'updated_by_user_id',
    ];

    protected $casts = [
        'sugerencias_accion' => 'array',
    ];

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(Indicator::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }
}
