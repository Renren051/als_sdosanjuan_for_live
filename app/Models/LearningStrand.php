<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningStrand extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'color_code',
        'icon_name',
        'order_seq',
    ];

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class, 'strand_id')->orderBy('module_number');
    }

    public function publishedModules(): HasMany
    {
        return $this->hasMany(Module::class, 'strand_id')
            ->where('is_published', true)
            ->orderBy('module_number');
    }
}
