<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\HtmlSanitizer;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register a custom Blade directive for HTML sanitization
        Blade::directive('sanitized', function ($expression) {
            return "<?php echo App\Helpers\HtmlSanitizer::sanitize($expression); ?>";
        });
    }
}
