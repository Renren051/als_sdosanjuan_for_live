<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuleProgress extends Model
{
    use HasFactory;

    protected $table = 'module_downloads_and_progress';

    public const STATUS_DOWNLOADED = 'downloaded';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'user_id',
        'module_id',
        'downloaded_at',
        'last_opened_at',
        'completion_status',
        'device_identifier',
        'last_page_read',
        'total_pages',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'downloaded_at' => 'datetime',
            'last_opened_at' => 'datetime',
            'synced_at' => 'datetime',
            'last_page_read' => 'integer',
            'total_pages' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function learner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function getProgressPercentAttribute(): int
    {
        if ($this->completion_status === self::STATUS_COMPLETED) {
            return 100;
        }

        if ($this->total_pages && $this->total_pages > 0) {
            return min(100, (int) round(($this->last_page_read / $this->total_pages) * 100));
        }

        return $this->completion_status === self::STATUS_IN_PROGRESS ? 50 : 0;
    }
}
