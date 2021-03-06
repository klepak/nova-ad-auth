<?php

namespace Klepak\NovaAdAuth\Listeners;

use Log;
use Exception;

use Adldap\Laravel\Events\AuthenticatedWithWindows;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

class SynchronizeUserPermissions
{
    /**
     * Handle the event.
     *
     * @param AuthenticatedWithWindows $event
     *
     * @return void
     */
    public function handle(AuthenticatedWithWindows $event)
    {
        info("Synchronizing user permissions for '{$event->user->getCommonName()}'.");

        $groupPermissions = config("auth-roles.ad_groups.permissions");
        $groupRoles = config("auth-roles.ad_groups.roles");

        $permissions = [];
        $roles = [];

        $groupNames = [];

        foreach($event->user->getGroups() as $group)
        {
            if(isset($group->cn[0]))
                $groupNames[] = $group->cn[0];
        }

        foreach($groupNames as $groupName)
        {
            foreach($groupPermissions as $guard => &$guardGroupPermissions)
            {
                if(!isset($permissions[$guard]))
                    $permissions[$guard] = [];

                if(isset($guardGroupPermissions[$groupName]))
                {
                    foreach($guardGroupPermissions[$groupName] as $permission)
                    {
                        if(!in_array($permission, $permissions[$guard]))
                            $permissions[$guard][] = $permission;
                    }
                }
            }

            foreach($groupRoles as $guard => &$guardGroupRoles)
            {
                if(!isset($roles[$guard]))
                    $roles[$guard] = [];

                if(isset($guardGroupRoles[$groupName]))
                {
                    foreach($guardGroupRoles[$groupName] as $role)
                    {
                        if(!in_array($role, $roles[$guard]))
                            $roles[$guard][] = $role;
                    }
                }
            }
        }

        $verifiedRoles = [];
        foreach($roles as $guard => $guardRoles)
        {
            if(!isset($verifiedRoles[$guard]))
                $verifiedRoles[$guard] = [];

            foreach($guardRoles as $role)
            {
                try
                {
                    $verifiedRole = Role::findByName($role, $guard);
                }
                catch(RoleDoesNotExist $e)
                {
                    Log::warning("Role $role does not exist on guard $guard");
                    continue;
                }

                $verifiedRoles[$guard][] = $verifiedRole;
            }
        }

        $verifiedPermissions = [];
        foreach($permissions as $guard => $guardPermissions)
        {
            if(!isset($verifiedPermissions[$guard]))
                $verifiedPermissions[$guard] = [];

            foreach($guardPermissions as $permission)
            {
                try
                {
                    $verifiedPermission = Permission::findByName($permission, $guard);
                }
                catch(PermissionDoesNotExist $e)
                {
                    Log::warning("Permission $permission does not exist on guard $guard");
                    continue;
                }

                $verifiedPermissions[$guard][] = $verifiedPermission;
            }
        }

        try
        {
            Log::info("Syncing " . count($verifiedRoles) . " roles and " . count($verifiedPermissions) . " permissions for '{$event->user->getCommonName()}'.");

            if(isset($verifiedRoles['web']))
                $event->model->syncRoles($verifiedRoles['web']);

            if(isset($verifiedPermissions['web']))
                $event->model->syncPermissions($verifiedPermissions['web']);

            if(isset($verifiedRoles['api']) || isset($verifiedPermissions['api']))
            {
                $apiUser = $event->model->apiUser()->get()->first();

                if($apiUser != null)
                {
                    if(isset($verifiedRoles['api']))
                        $apiUser->syncRoles($verifiedRoles['api']);

                    if(isset($verifiedPermissions['api']))
                        $apiUser->syncPermissions($verifiedPermissions['api']);
                }
            }
        }
        catch(Exception $e)
        {
            Log::error("Failed to sync roles and permissions", ['exception' => $e]);
        }

    }
}
