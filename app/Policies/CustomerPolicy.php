<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('View Customers');
    }

    public function view(User $user, Customer $model)
    {
        return $user->hasPermissionTo('View Customers');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('Create Customers');
    }

    public function update(User $user, Customer $model)
    {
        return $user->hasPermissionTo('Edit Customers');
    }

    public function delete(User $user, Customer $model)
    {
        return $user->hasPermissionTo('Delete Customers');
    }

    public function export(User $user)
    {
        return $user->hasPermissionTo('Export Customers');
    }
}
