Add provider after Nova provider in app.php
Klepak\NovaAdAuth\AdAuthenticationServiceProvider::class,

Add to EventServiceProvider:

use Adldap\Laravel\Events\Synchronizing;
use Klepak\NovaAdAuth\Listeners\SynchronizeUserPermissions;

    to $listen:
        Synchronizing::class => [
            SynchronizeUserPermissions::class
        ],


Add to routeMiddleware in Kernel.php

'auth.sso' => \Adldap\Laravel\Middleware\WindowsAuthenticate::class,
'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,

**NOTE: this will replace your auth.php**
php artisan vendor:publish --provider="Klepak\NovaAdAuth\AdAuthenticationServiceProvider" --force
