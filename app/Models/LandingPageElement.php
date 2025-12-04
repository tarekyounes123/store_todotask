<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageElement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'element_type',
        'content',
        'attributes',
        'position',
        'section_id',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'attributes' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'section_id' => 'integer',
    ];

    public function section()
    {
        return $this->belongsTo(LandingPageSection::class, 'section_id');
    }
}
