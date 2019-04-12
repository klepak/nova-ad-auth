# Install
    composer require nova-ad-auth
  
**Add package provider after Nova provider in app.php**  

```php
    'providers' => [
    
        ...

        App\Providers\NovaServiceProvider::class,
        Klepak\NovaAdAuth\AdAuthenticationServiceProvider::class,
    ];
```

**Add to EventServiceProvider**  

```php
    use Adldap\Laravel\Events\AuthenticatedWithWindows;
    use Klepak\NovaAdAuth\Listeners\SynchronizeUserPermissions;

    ...

    protected $listen = [
        
        ...

        AuthenticatedWithWindows::class => [
            SynchronizeUserPermissions::class
        ],
    ];
```

**Add route middleware in Kernel.php**  

```php
protected $routeMiddleware = [
    
    ...

    'auth.sso' => \Adldap\Laravel\Middleware\WindowsAuthenticate::class,
    'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
    'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
];
```

**Publish assets**  

    php artisan vendor:publish --provider="Klepak\NovaAdAuth\AdAuthenticationServiceProvider" --force
  
**NOTE: this will replace your existing auth and adldap config**

## Configure SSO

- Create a directory in your public folder called sso
- Copy your index.php to this directory, and add an additional ../ to all paths
- Create file called web.config in this directory, with following contents:

```xml
    <?xml version="1.0" encoding="UTF-8"?>
    <configuration>
        <system.webServer>
            <security>
                <authentication>
                    <windowsAuthentication enabled="true" />
                    <anonymousAuthentication enabled="false" />
                </authentication>
            </security>
            <rewrite>
                <rules>
                    <clear />
                    <rule name="Rewrite" enabled="true" stopProcessing="true">
                        <match url="^(?!storage)" ignoreCase="false" />
                        <conditions logicalGrouping="MatchAll" trackAllCaptures="false">
                            <add input="{REQUEST_FILENAME}" matchType="IsFile" ignoreCase="false" negate="true" />
                        </conditions>
                        <action type="Rewrite" url="index.php" appendQueryString="true" />
                    </rule>
                </rules>
            </rewrite>
        </system.webServer>
    </configuration>
```
