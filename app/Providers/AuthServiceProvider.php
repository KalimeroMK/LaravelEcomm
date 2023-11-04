<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Modules\Post\Models\Policies\PostCommentPolicy;
use Modules\Post\Models\PostComment;
use Modules\Role\Models\Polices\RolePolicy;
use Modules\Role\Models\Role;
use Modules\Settings\Models\Polices\SettingsPolicy;
use Modules\Settings\Models\Setting;
use Modules\User\Models\Policies\UserPolicy;
use Modules\User\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        PostComment::class => PostCommentPolicy::class,
        User::class => UserPolicy::class,
        Setting::class => SettingsPolicy::class,
        Role::class => RolePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();
//        Gate::before(function ($user, $ability) {
//            return $user->hasRole('super-admin') ? true : null;
//        });
    }
}
