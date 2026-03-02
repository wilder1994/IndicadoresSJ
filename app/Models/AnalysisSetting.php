<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalysisSetting extends Model
{
    use HasFactory;

    public const MODE_RULES = 'rules';
    public const MODE_LOCAL = 'local_ai';
    public const MODE_OPENAI = 'openai';

    protected $fillable = [
        'mode',
        'rules_enabled',
        'local_endpoint_url',
        'local_model',
        'local_timeout_ms',
        'openai_model',
        'openai_timeout_ms',
        'updated_by_user_id',
    ];

    protected $casts = [
        'rules_enabled' => 'boolean',
    ];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }
}
