<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Module extends Model
{
    use HasFactory, SoftDeletes;

    public const LEVEL_BLP = 'BLP';
    public const LEVEL_ELEMENTARY = 'Elementary';
    public const LEVEL_JUNIOR_HIGH = 'Junior_High';

    public const SUB_LEVEL_BLP = 'Basic Literacy';
    public const SUB_LEVEL_LOWER_ELEMENTARY = 'Lower Elementary';
    public const SUB_LEVEL_ADVANCED_ELEMENTARY = 'Advanced Elementary';
    public const SUB_LEVEL_JUNIOR_HIGH = 'Junior High School';

    public const LANGUAGE_ENGLISH = 'English';
    public const LANGUAGE_FILIPINO = 'Filipino';

    protected $fillable = [
        'strand_id',
        'level',
        'sub_level',
        'language',
        'module_number',
        'title',
        'description',
        'pdf_file_path',
        'pdf_file_size',
        'version',
        'is_published',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'pdf_file_size' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function strand(): BelongsTo
    {
        return $this->belongsTo(LearningStrand::class, 'strand_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function progressRecords(): HasMany
    {
        return $this->hasMany(ModuleProgress::class, 'module_id');
    }

    public function gameAttempts(): HasMany
    {
        return $this->hasMany(ModuleGameAttempt::class, 'module_id');
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->pdf_file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    public function getDownloadUrlAttribute(): string
    {
        return url("/api/v1/modules/{$this->id}/download");
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeForLevel($query, string $level)
    {
        return $query->where('level', $level);
    }

    public function scopeForStrand($query, int $strandId)
    {
        return $query->where('strand_id', $strandId);
    }

    public function scopeForSubLevel($query, string $subLevel)
    {
        return $query->where('sub_level', $subLevel);
    }

    public function scopeForLanguage($query, string $language)
    {
        return $query->where('language', $language);
    }
}
