# Install
    composer require nova-ad-auth
  
**Add package provider after Nova provider in app.php**  

    'providers' => [
    
        ...

        App\Providers\NovaServiceProvider::class,
        Klepak\NovaAdAuth\AdAuthenticationServiceProvider::class,
    ];

**Add to EventServiceProvider**  

    use Adldap\Laravel\Events\Synchronizing;
    use Klepak\NovaAdAuth\Listeners\SynchronizeUserPermissions;

    protected $listen = [
        
        ...

        Synchronizing::class => [
            SynchronizeUserPermissions::class
        ],
    ];


**Add route middleware in Kernel.php**  

    'auth.sso' => \Adldap\Laravel\Middleware\WindowsAuthenticate::class,
    'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
    'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,


**Publish assets**  

    php artisan vendor:publish --provider="Klepak\NovaAdAuth\AdAuthenticationServiceProvider" --force
  
**NOTE: this will replace your existing auth and adldap config**
