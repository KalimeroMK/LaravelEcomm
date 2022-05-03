<?php

    namespace App\Providers;

    use Illuminate\Support\ServiceProvider;
    use Modules\User\Models\User;
    use Modules\User\Observers\UserObserver;

    class ObserverServiceProvider extends ServiceProvider
    {
        public function register()
        {
            //
        }

        public function boot()
        {
            User::observe(UserObserver::class);
        }
    }
