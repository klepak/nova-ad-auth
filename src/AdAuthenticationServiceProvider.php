<?php

namespace Klepak\NovaAdAuth;

use Illuminate\Support\ServiceProvider;
use Klepak\NovaAdAuth\Console\Commands\SyncRolesPermissions;

use Illuminate\Support\Facades\Route;
use Klepak\NovaAdAuth\Console\Commands\VerifyPoliciesCommand;
use Klepak\NovaAdAuth\Console\Commands\Scaffolding\StandardPolicyMakeCommand;

class AdAuthenticationServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to package controller routes.
     *
     * @var string
     */
    protected $routeNamespace = 'Klepak\\NovaAdAuth\\Http\\Controllers';

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

        Route::middleware('web')
             ->namespace($this->routeNamespace)
             ->group(__DIR__.'/../routes/web.php');

        $this->publishes([
            __DIR__.'/../config/auth.php' => config_path('auth.php'),
            __DIR__.'/../config/auth-roles.php' => config_path('auth-roles.php'),
            __DIR__.'/../config/ldap.php' => config_path('ldap.php'),
            __DIR__.'/../config/ldap_auth.php' => config_path('ldap_auth.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                SyncRolesPermissions::class,
                VerifyPoliciesCommand::class,
                StandardPolicyMakeCommand::class,
            ]);
        }
    }
}
