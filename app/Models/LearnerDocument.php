<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class LearnerDocument extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_PSA_BIRTH_CERT = 'psa_birth_cert';
    public const TYPE_BARANGAY_CLEARANCE = 'barangay_clearance';
    public const TYPE_FORM_137_SF10 = 'form_137_sf10';
    public const TYPE_PHOTO_ID = 'photo_id';
    public const TYPE_PORTFOLIO_DOC = 'portfolio_document';

    public const STATUS_PENDING = 'pending';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'enrollment_id',
        'document_type',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'verification_status',
        'verification_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getDocumentTypeLabelAttribute(): string
    {
        return match ($this->document_type) {
            self::TYPE_PSA_BIRTH_CERT => 'PSA Birth Certificate',
            self::TYPE_BARANGAY_CLEARANCE => 'Barangay Clearance / Certificate of Residency',
            self::TYPE_FORM_137_SF10 => 'DepEd Form 137 / School Form 10 (SF10)',
            self::TYPE_PHOTO_ID => '2x2 Recent Identification Photo',
            self::TYPE_PORTFOLIO_DOC => 'Portfolio / Assessment Document',
            default => str_replace('_', ' ', ucfirst($this->document_type)),
        };
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }
}
