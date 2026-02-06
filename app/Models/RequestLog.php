<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestLog extends Model
{
    /** @use HasFactory<\Database\Factories\RequestLogFactory> */
    use HasFactory;

    protected $fillable = [
        'mock_id',
        'method',
        'path',
        'headers',
        'query_params',
        'body',
        'response_status',
        'response_headers',
        'response_body',
        'response_time_ms',
        'was_matched',
    ];

    protected $casts = [
        'headers' => 'array',
        'query_params' => 'array',
        'response_headers' => 'array',
        'was_matched' => 'boolean',
        'response_time_ms' => 'integer',
    ];

    public function mock(): BelongsTo
    {
        return $this->belongsTo(Mock::class);
    }

    public function scopeRecent($query, int $limit = 100)
    {
        return $query->orderByDesc('created_at')->limit($limit);
    }

    public function scopeMatched($query)
    {
        return $query->where('was_matched', true);
    }

    public function scopeUnmatched($query)
    {
        return $query->where('was_matched', false);
    }
}
