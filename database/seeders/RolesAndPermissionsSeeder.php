<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view assets',
            'create assets',
            'edit assets',
            'delete assets',

            'view rooms',
            'create rooms',
            'edit rooms',
            'delete rooms',

            'view loans',
            'create loans',
            'edit loans',
            'delete loans',

            'view maintenances',
            'create maintenances',
            'edit maintenances',
            'delete maintenances',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $admin->syncPermissions(
            Permission::where('guard_name', 'web')->get()
        );

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
