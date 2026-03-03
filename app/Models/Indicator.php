<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Indicator extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'unit',
        'target_value',
        'target_operator',
        'frequency',
        'formula_description',
        'required_fields',
        'allows_over_100',
        'is_active',
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'required_fields' => 'array',
        'allows_over_100' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function dashboardWeight(): HasOne
    {
        return $this->hasOne(DashboardWeight::class);
    }

    public function captures(): HasMany
    {
        return $this->hasMany(IndicatorCapture::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function analysisTemplate(): HasOne
    {
        return $this->hasOne(AnalysisTemplate::class);
    }
}
