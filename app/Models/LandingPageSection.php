<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
        'content',
        'section_type',
        'settings',
        'position',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'settings' => 'array',
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];

    public function elements()
    {
        return $this->hasMany(LandingPageElement::class, 'section_id');
    }
}
