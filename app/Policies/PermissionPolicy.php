<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;

class PermissionPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('View Permissions');
    }

    public function view(User $user, Permission $permission)
    {
        return $user->hasPermissionTo('View Permissions');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('Create Permissions');
    }

    public function update(User $user, Permission $permission)
    {
        return $user->hasPermissionTo('Edit Permissions');
    }

    public function delete(User $user, Permission $permission)
    {
        return $user->hasPermissionTo('Delete Permissions');
    }
}
