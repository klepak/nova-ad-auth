<?php

namespace Klepak\LaravelAuth;

use Illuminate\Support\ServiceProvider;
use Klepak\LaravelAuth\Console\Commands\SyncRolesPermissions;

class AuthenticationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->publishes([
            __DIR__.'/../config/auth-roles.php' => config_path('auth-roles.php'),
            __DIR__.'/../config/ldap.php' => config_path('ldap.php'),
            __DIR__.'/../config/ldap_auth.php' => config_path('ldap_auth.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                SyncRolesPermissions::class
            ]);
        }
    }
}
