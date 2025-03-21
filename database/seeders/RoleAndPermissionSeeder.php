<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Admin;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        // only superadmin
        Permission::create(['name' => 'view admins']);
        Permission::create(['name' => 'create admins']);
        Permission::create(['name' => 'edit admins']);
        Permission::create(['name' => 'delete admins']);

        // only superadmin and admin
        Permission::create(['name' => 'view managers']);
        Permission::create(['name' => 'create managers']);
        Permission::create(['name' => 'edit managers']);
        Permission::create(['name' => 'delete managers']);

        // only superadmin and admin and manager
        Permission::create(['name' => 'view planners']);
        Permission::create(['name' => 'create planners']);
        Permission::create(['name' => 'edit planners']);
        Permission::create(['name' => 'delete planners']);

        // only superadmin and admin and manager and planner
        Permission::create(['name' => 'view employees']);
        Permission::create(['name' => 'create employees']);
        Permission::create(['name' => 'edit employees']);
        Permission::create(['name' => 'delete employees']);

        // only superadmin
        Permission::create(['name' => 'view companies']);
        Permission::create(['name' => 'create companies']);
        Permission::create(['name' => 'edit companies']);
        Permission::create(['name' => 'delete companies']);

        // only admin
        Permission::create(['name' => 'view transaction categories']);
        Permission::create(['name' => 'create transaction categories']);
        Permission::create(['name' => 'edit transaction categories']);
        Permission::create(['name' => 'delete transaction categories']);

        // Create roles and assign permissions

        // SuperAdmin (Eng yuqori admin)
        $superAdminRole = Role::create(['name' => 'superadmin']);
        $superAdminRole->givePermissionTo([
            'view admins',
            'create admins',
            'edit admins',
            'delete admins',
            'view managers',
            'edit managers',
            'delete managers',
            'view planners',
            'edit planners',
            'delete planners',
            'view employees',
            'edit employees',
            'delete employees',
            'view companies',
            'create companies',
            'edit companies',
            'delete companies'
        ]);

        // Admin (Korxona egasi)
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo([
            'view managers',
            'create managers',
            'edit managers',
            'delete managers',
            'view planners',
            'create planners',
            'edit planners',
            'delete planners',
            'view employees',
            'create employees',
            'edit employees',
            'delete employees',
            'view transaction categories',
            'create transaction categories',
            'edit transaction categories',
            'delete transaction categories'
        ]);

        // Manager (Ish boshqaruvchi)
        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo([
            'view planners',
            'create planners',
            'edit planners',
            'delete planners',
            'view employees',
            'create employees',
            'edit employees',
            'delete employees'
        ]);

        // Planner (Rejalashtiruvchi)
        $plannerRole = Role::create(['name' => 'planner']);
        $plannerRole->givePermissionTo([
            'view employees',
            'create employees',
            'edit employees',
            'delete employees'
        ]);

        // Employee (Oddiy xodim)
        $employeeRole = Role::create(['name' => 'employee']);
        $employeeRole->givePermissionTo([

        ]);

        // Create default superadmin user
        $user = User::create([
            'phone' => '998000000000',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('superadmin');
    }
}
