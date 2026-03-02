<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Validation\ValidationException;

class IndicatorCapture extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (self $capture): void {
            $period = $capture->period()->first();
            if ($period && $period->isClosed()) {
                throw ValidationException::withMessages([
                    'period_id' => 'El periodo esta cerrado y no permite ediciones.',
                ]);
            }
        });
    }

    protected $fillable = [
        'indicator_id',
        'zone_id',
        'period_id',
        'input_data',
        'numerator',
        'denominator',
        'result_percentage',
        'complies',
        'analysis_text',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    protected $casts = [
        'input_data' => 'array',
        'numerator' => 'decimal:2',
        'denominator' => 'decimal:2',
        'result_percentage' => 'decimal:2',
        'complies' => 'boolean',
    ];

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(Indicator::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public function improvement(): HasOne
    {
        return $this->hasOne(Improvement::class);
    }
}
