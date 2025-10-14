<?php

declare(strict_types=1);

namespace Modules\Core\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class RTLServiceProvider extends ServiceProvider
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
        // RTL Blade directive
        Blade::directive('rtl', function ($expression) {
            return "<?php if(app()->getLocale() === 'ar' || (config('app.locales')[app()->getLocale()]['rtl'] ?? false)): ?>";
        });

        Blade::directive('endrtl', function () {
            return "<?php endif; ?>";
        });

        // LTR Blade directive
        Blade::directive('ltr', function ($expression) {
            return "<?php if(!(app()->getLocale() === 'ar' || (config('app.locales')[app()->getLocale()]['rtl'] ?? false))): ?>";
        });

        Blade::directive('endltr', function () {
            return "<?php endif; ?>";
        });

        // Locale Blade directive
        Blade::directive('locale', function ($expression) {
            return "<?php if(app()->getLocale() === {$expression}): ?>";
        });

        Blade::directive('endlocale', function () {
            return "<?php endif; ?>";
        });

        // Translation Blade directive
        Blade::directive('trans', function ($expression) {
            return "<?php echo __({$expression}); ?>";
        });

        // Pluralization Blade directive
        Blade::directive('transchoice', function ($expression) {
            return "<?php echo trans_choice({$expression}); ?>";
        });
    }
}
