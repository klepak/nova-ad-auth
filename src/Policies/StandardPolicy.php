<?php

namespace Klepak\NovaAdAuth\Policies;

use Klepak\NovaAdAuth\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

use Exception;

abstract class StandardPolicy
{
    use HandlesAuthorization;

    protected $permissionEntity = null;

    private function methodToNode($method)
    {
        return str_replace('_',' ', snake_case($method));
    }

    public function can($user, $model = null, $operation = null)
    {
        $permissionNode = $this->getPermissionNode($model, $operation);

        return $user->can($permissionNode);
    }

    public function getPermissionNode($model = null, $operation = null)
    {
        if($operation == null)
            $operation = $this->methodToNode(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function']);

        if($model == null)
            $model = $this;

        $modelFullyQualifiedClass = get_class($model);
        $modelFqcSegments = explode('\\', $modelFullyQualifiedClass);
        $modelClassName = end($modelFqcSegments);

        if(substr($modelClassName, -6) == 'Policy')
            $modelClassName = substr($modelClassName, 0, strlen($modelClassName)-6);

        $modelSentenceName = str_plural(str_replace('_', ' ', snake_case($modelClassName)));

        if($modelSentenceName == null)
            throw new Exception('Failed to determine permission entity');

        return "$operation $modelSentenceName";
    }

    public function getAllNodes()
    {
        $methods = collect(get_class_methods($this));

        return $methods->filter(function($value) {
            return !in_array($value, [
                'can','getAllNodes','allow','deny','methodToNode','getPermissionNode','viewAny'
            ]);
        })->map(function($value) {
            return $this->getPermissionNode(null, $this->methodToNode($value));
        });
    }

    public function viewAny($user)
    {
        return $this->view($user, null);
    }


    // ------------------------------------ //

    public function view(User $user, $model)
    {
        return $this->can($user, $model, 'view');
    }

    public function create(User $user)
    {
        return $this->can($user, null, 'create');
    }

    public function update(User $user, $model)
    {
        return $this->can($user, $model, 'update');
    }

    public function delete(User $user, $model)
    {
        return $this->can($user, $model, 'delete');
    }

    public function restore(User $user, $model)
    {
        return $this->can($user, $model, 'restore');
    }

    public function forceDelete(User $user, $model)
    {
        return $this->can($user, $model, 'forceDelete');
    }
}
