<?php

namespace Modules\Tenant\Providers;

use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Modules\Core\Traits\AutoRegistersCommands;
use Modules\Tenant\Models\Tenant;

class TenantServiceProvider extends ServiceProvider
{
    use AutoRegistersCommands;

    protected string $moduleName = 'Tenant';

    protected string $moduleNameLower = 'tenant';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        if (config('tenant.multi_tenant.enabled')) {
            $this->registerCommands();
            $this->registerCommandSchedules();
            $this->registerTranslations();
            $this->registerViews();
            $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
            $this->autoRegisterCommands($this->moduleName);
            $this->configureTenant();
            $this->configureQueue();
        }
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        if (config('tenant.multi_tenant.enabled')) {
            $this->app->register(RouteServiceProvider::class);
        }
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(module_path($this->moduleName, 'Resources/lang'));
        }
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->moduleNameLower);
        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->moduleNameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);

        $componentNamespace = str_replace(
            '/',
            '\\',
            config('modules.namespace').'\\'.$this->moduleName.'\\'.config(
                'modules.paths.generator.component-class.path'
            )
        );
        Blade::componentNamespace($componentNamespace, $this->moduleNameLower);
    }

    /**
     * Gets the publishable view paths for the module.
     *
     * @return array<string> Array of paths.
     */
    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (Config::get('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->moduleNameLower)) {
                $paths[] = $path.'/modules/'.$this->moduleNameLower;
            }
        }

        return $paths;
    }


    /**
     * Gets the publishable view paths for the module.
     *
     * @return array<string> Array of paths.
     */
    public function provides(): array
    {
        return [];
    }

    /**
     * Configure tenant based on the domain.
     */
    protected function configureTenant(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        $host = request()->getHost();
        $tenant = Tenant::whereDomain($host)->firstOrFail();
        $tenant->configure()->use();  // Set up and use tenant configuration
    }

    /**
     * Configure the queue system to be aware of tenants.
     */
    protected function configureQueue(): void
    {
        Queue::createPayloadUsing(function () {
            if ($this->app->bound('tenant')) {
                $tenant = $this->app->make('tenant');
                return ['tenant_id' => $tenant->id];
            }
            return [];
        });

        Event::listen(JobProcessing::class, function (JobProcessing $event) {
            $tenantId = $event->job->payload()['tenant_id'] ?? null;
            if ($tenantId) {
                $tenant = Tenant::find($tenantId);
                $tenant?->configure()->use();
            }
        });
    }
}
