<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'purchases.view',
            'purchases.create',
            'purchases.update',
            'purchases.delete',
            'sales.view',
            'sales.create',
            'sales.update',
            'sales.delete',
            'products.view',
            'products.create',
            'products.update',
            'products.delete',
            'reports.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $operator = Role::firstOrCreate(['name' => 'operator']);

        $admin->syncPermissions($permissions);
        $manager->syncPermissions([
            'purchases.view', 'purchases.create', 'purchases.update',
            'sales.view', 'sales.create', 'sales.update',
            'products.view', 'products.update',
            'reports.view',
        ]);
        $operator->syncPermissions([
            'purchases.view', 'purchases.create',
            'sales.view', 'sales.create',
            'products.view',
        ]);
    }
}
