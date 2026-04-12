<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('View Roles');
    }

    public function view(User $user, Role $role)
    {
        return $user->hasPermissionTo('View Roles');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('Create Roles'); // I should probably add these to seeder too if they don't exist
    }

    public function update(User $user, Role $role)
    {
        return $user->hasPermissionTo('Edit Roles');
    }

    public function delete(User $user, Role $role)
    {
        return $user->hasPermissionTo('Delete Roles');
    }
}
