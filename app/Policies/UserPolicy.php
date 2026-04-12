<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('View Users');
    }

    public function view(User $user, User $model)
    {
        return $user->hasPermissionTo('View Users') || $user->id === $model->id;
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('Create Users');
    }

    public function update(User $user, User $model)
    {
        return $user->hasPermissionTo('Edit Users') || $user->id === $model->id;
    }

    public function delete(User $user, User $model)
    {
        return $user->id !== $model->id && $user->hasPermissionTo('Delete Users');
    }

    public function export(User $user)
    {
        return $user->hasPermissionTo('Export Users');
    }
}
