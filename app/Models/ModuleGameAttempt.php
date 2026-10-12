<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuleGameAttempt extends Model
{
    protected $fillable = [
        'attempt_uuid',
        'user_id',
        'module_id',
        'game_type',
        'score',
        'max_score',
        'moves',
        'elapsed_seconds',
        'device_identifier',
        'attempted_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'max_score' => 'integer',
            'moves' => 'integer',
            'elapsed_seconds' => 'integer',
            'attempted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'module_id');
    }
}
