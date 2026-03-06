<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Improvement extends Model
{
    use HasFactory;

    protected $fillable = [
        'indicator_capture_id',
        'indicator_id',
        'zone_id',
        'period_id',
        'analysis',
        'action_taken',
        'action_defined',
        'improvement_required',
        'integrated_analysis_block',
        'created_by_user_id',
    ];

    public function capture(): BelongsTo
    {
        return $this->belongsTo(IndicatorCapture::class, 'indicator_capture_id');
    }

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
}
