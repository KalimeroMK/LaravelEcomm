<?php

    namespace App\Providers;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\View;
    use Illuminate\Support\ServiceProvider;
    use Modules\Front\Http\ViewComposers\MenuViewComposer;
    use Modules\Front\Http\ViewComposers\SchemaOrgViewComposer;
    use Modules\Front\Http\ViewComposers\SettingsViewComposer;
    use Schema;

    class AppServiceProvider extends ServiceProvider
    {
        /**
         * Register any application services.
         *
         * @return void
         */
        public function register(): void
        {
            //
        }

        /**
         * Bootstrap any application services.
         *
         * @return void
         */
        public function boot(): void
        {
            Model::preventLazyLoading(! app()->isProduction());
            Schema::defaultStringLength(191);
            View::composer(['front::layouts.header', 'front::layouts.footer', 'front::pages.about-us'], SettingsViewComposer::class);
            View::composer(['front::pages.product-grids', 'front::layouts.header'], MenuViewComposer::class);
            View::composer('front::layouts.master', SchemaOrgViewComposer::class);
        }
    }
