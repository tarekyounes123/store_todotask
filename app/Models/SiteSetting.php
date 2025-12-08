<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'setting_key',
        'setting_value',
        'description'
    ];

    protected $casts = [
        'setting_value' => 'array', // Store as JSON/array
    ];

    public const FOOTER_SETTINGS_KEY = 'footer_content';
    public const SITE_SETTINGS_KEY = 'site_settings';
    public const LOGO_SETTINGS_KEY = 'logo_settings';
    public const TITLE_SETTINGS_KEY = 'title_settings';
    public const FEATURED_PRODUCTS_SETTINGS_KEY = 'featured_products';
}
