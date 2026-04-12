<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'Create Suppliers', 'slug' => 'create-suppliers'],
            ['name' => 'Edit Suppliers', 'slug' => 'edit-suppliers'],
            ['name' => 'Delete Suppliers', 'slug' => 'delete-suppliers'],
            ['name' => 'View Suppliers', 'slug' => 'view-suppliers'],
            ['name' => 'Create Customers', 'slug' => 'create-customers'],
            ['name' => 'Edit Customers', 'slug' => 'edit-customers'],
            ['name' => 'Delete Customers', 'slug' => 'delete-customers'],
            ['name' => 'View Customers', 'slug' => 'view-customers'],
            ['name' => 'View Users', 'slug' => 'view-users'],
            ['name' => 'Create Users', 'slug' => 'create-users'],
            ['name' => 'Edit Users', 'slug' => 'edit-users'],
            ['name' => 'Delete Users', 'slug' => 'delete-users'],
            ['name' => 'Export Users', 'slug' => 'export-users'],
            ['name' => 'Export Suppliers', 'slug' => 'export-suppliers'],
            ['name' => 'Export Customers', 'slug' => 'export-customers'],
            ['name' => 'View Roles', 'slug' => 'view-roles'],
            ['name' => 'Create Roles', 'slug' => 'create-roles'],
            ['name' => 'Edit Roles', 'slug' => 'edit-roles'],
            ['name' => 'Delete Roles', 'slug' => 'delete-roles'],
            ['name' => 'View Permissions', 'slug' => 'view-permissions'],
            ['name' => 'Create Permissions', 'slug' => 'create-permissions'],
            ['name' => 'Edit Permissions', 'slug' => 'edit-permissions'],
            ['name' => 'Delete Permissions', 'slug' => 'delete-permissions'],
        ];
        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['slug' => $permission['slug']], $permission);
        }
    }
}
