<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mock extends Model
{
    /** @use HasFactory<\Database\Factories\MockFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'match_rules',
        'response_config',
        'priority',
        'is_active',
        'scenario_id',
    ];

    protected $casts = [
        'match_rules' => 'array',
        'response_config' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    public function scenario(): BelongsTo
    {
        return $this->belongsTo(Scenario::class);
    }

    public function requestLogs(): HasMany
    {
        return $this->hasMany(RequestLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrderedByPriority($query)
    {
        return $query->orderByDesc('priority')->orderByDesc('created_at');
    }
}
