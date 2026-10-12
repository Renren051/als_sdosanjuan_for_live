<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HeroBanner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'image_path',
        'position_x',
        'position_y',
        'zoom',
        'button_text',
        'button_url',
        'order_seq',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order_seq' => 'integer',
        'position_x' => 'integer',
        'position_y' => 'integer',
        'zoom' => 'integer',
    ];

    /**
     * Get CSS object-position string (e.g. '50% 25%').
     */
    public function getObjectPositionAttribute(): string
    {
        $x = $this->position_x ?? 50;
        $y = $this->position_y ?? 50;
        return "{$x}% {$y}%";
    }

    /**
     * Get accessible URL for the banner image.
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->image_path)) {
            return asset('images/banners/hero-slide-1.jpg');
        }

        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        if (file_exists(public_path($this->image_path))) {
            return asset($this->image_path);
        }

        if (Storage::disk('public')->exists($this->image_path)) {
            return Storage::disk('public')->url($this->image_path);
        }

        return asset($this->image_path);
    }
}
