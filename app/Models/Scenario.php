<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Scenario extends Model
{
    /** @use HasFactory<\Database\Factories\ScenarioFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'current_state',
        'state_machine',
        'is_active',
    ];

    protected $casts = [
        'state_machine' => 'array',
        'is_active' => 'boolean',
    ];

    public function mocks(): HasMany
    {
        return $this->hasMany(Mock::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
