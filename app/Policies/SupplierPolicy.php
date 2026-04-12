<?php

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;

class SupplierPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('View Suppliers');
    }

    public function view(User $user, Supplier $model)
    {
        return $user->hasPermissionTo('View Suppliers');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('Create Suppliers');
    }

    public function update(User $user, Supplier $model)
    {
        return $user->hasPermissionTo('Edit Suppliers');
    }

    public function delete(User $user, Supplier $model)
    {
        return $user->hasPermissionTo('Delete Suppliers');
    }

    public function export(User $user)
    {
        return $user->hasPermissionTo('Export Suppliers');
    }
}
