<?php

namespace Klepak\LaravelAuth\Console\Commands;

use Illuminate\Console\Command;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

class SyncRolesPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initializes and updates roles and permissions based on config.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $roles = config("auth-roles.roles");
        $permissions = config("auth-roles.permissions");

        if(count($roles) == 0)
            echo "No roles in config\n";

        if(count($permissions) == 0)
            echo "No permissions in config\n";

        // check for missing roles
        $missingRoles = [];
        $existingRoles = [];
        foreach($roles as $guard => $guardRoles)
        {
            if(!isset($existingRoles[$guard]))
                $existingRoles[$guard] = [];

            if(!isset($missingRoles[$guard]))
                $missingRoles[$guard] = [];

            foreach($guardRoles as $role => $rolePermissions)
            {
                try
                {
                    if(!isset($existingRoles[$guard][$role]))
                        $existingRoles[$guard][$role] = Role::findByName($role, $guard);
                }
                catch(RoleDoesNotExist $e)
                {
                    if(!in_array($role, $missingRoles[$guard]))
                        $missingRoles[$guard][] = $role;
                }
            }
        }

        // check for missing permissions
        $missingPermissions = [];
        $existingPermissions = [];
        foreach($permissions as $guard => $guardPermissions)
        {
            if(!isset($existingPermissions[$guard]))
                $existingPermissions[$guard] = [];

            if(!isset($missingPermissions[$guard]))
                $missingPermissions[$guard] = [];

            foreach($guardPermissions as $permission)
            {
                try
                {
                    #echo "Check $guard -> $permission\n";
                    if(!isset($existingPermissions[$guard][$permission]))
                        $existingPermissions[$guard][$permission] = Permission::findByName($permission, $guard);
                }
                catch(PermissionDoesNotExist $e)
                {
                    if(!in_array($permission, $missingPermissions[$guard]))
                        $missingPermissions[$guard][] = $permission;
                }
            }
        }

        // create missing roles
        foreach($missingRoles as $guard => $guardMissingRoles)
        {
            foreach($guardMissingRoles as $missingRole)
            {
                $this->call("permission:create-role", [
                    "name" => $missingRole,
                    "guard" => $guard
                ]);
            }
        }

        // create missing permissions
        foreach($missingPermissions as $guard => $guardMissingPermissions)
        {
            foreach($guardMissingPermissions as $missingPermission)
            {
                $this->call("permission:create-permission", [
                    "name" => $missingPermission,
                    "guard" => $guard
                ]);
            }
        }

        // associate permissions to roles
        foreach($roles as $guard => $guardRoles)
        {
            foreach($guardRoles as $roleName => $rolePermissions)
            {
                echo "Sync permissions for $roleName - $guard\n";
                $role = Role::findByName($roleName, $guard);
                $role->syncPermissions($rolePermissions);
            }
        }
    }
}
