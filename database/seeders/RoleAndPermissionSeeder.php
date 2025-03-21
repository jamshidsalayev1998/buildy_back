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
        if (!Permission::where('name', 'view admins')->exists()) {
            Permission::create(['name' => 'view admins']);
        }
        if (!Permission::where('name', 'create admins')->exists()) {
            Permission::create(['name' => 'create admins']);
        }
        if (!Permission::where('name', 'edit admins')->exists()) {
            Permission::create(['name' => 'edit admins']);
        }
        if (!Permission::where('name', 'delete admins')->exists()) {
            Permission::create(['name' => 'delete admins']);
        }

        // only superadmin and admin
        if (!Permission::where('name', 'view managers')->exists()) {
            Permission::create(['name' => 'view managers']);
        }
        if (!Permission::where('name', 'create managers')->exists()) {
            Permission::create(['name' => 'create managers']);
        }
        if (!Permission::where('name', 'edit managers')->exists()) {
            Permission::create(['name' => 'edit managers']);
        }
        if (!Permission::where('name', 'delete managers')->exists()) {
            Permission::create(['name' => 'delete managers']);
        }

        // only superadmin and admin and manager
        if (!Permission::where('name', 'view planners')->exists()) {
            Permission::create(['name' => 'view planners']);
        }
        if (!Permission::where('name', 'create planners')->exists()) {
            Permission::create(['name' => 'create planners']);
        }
        if (!Permission::where('name', 'edit planners')->exists()) {
            Permission::create(['name' => 'edit planners']);
        }
        if (!Permission::where('name', 'delete planners')->exists()) {
            Permission::create(['name' => 'delete planners']);
        }

        // only superadmin and admin and manager and planner
        if (!Permission::where('name', 'view employees')->exists()) {
            Permission::create(['name' => 'view employees']);
        }
        if (!Permission::where('name', 'create employees')->exists()) {
            Permission::create(['name' => 'create employees']);
        }
        if (!Permission::where('name', 'edit employees')->exists()) {
            Permission::create(['name' => 'edit employees']);
        }
        if (!Permission::where('name', 'delete employees')->exists()) {
            Permission::create(['name' => 'delete employees']);
        }

        // only superadmin
        if (!Permission::where('name', 'view companies')->exists()) {
            Permission::create(['name' => 'view companies']);
        }
        if (!Permission::where('name', 'create companies')->exists()) {
            Permission::create(['name' => 'create companies']);
        }
        if (!Permission::where('name', 'edit companies')->exists()) {
            Permission::create(['name' => 'edit companies']);
        }
        if (!Permission::where('name', 'delete companies')->exists()) {
            Permission::create(['name' => 'delete companies']);
        }

        // only admin
        if (!Permission::where('name', 'view transaction categories')->exists()) {
            Permission::create(['name' => 'view transaction categories']);
        }
        if (!Permission::where('name', 'create transaction categories')->exists()) {
            Permission::create(['name' => 'create transaction categories']);
        }
        if (!Permission::where('name', 'edit transaction categories')->exists()) {
            Permission::create(['name' => 'edit transaction categories']);
        }
        if (!Permission::where('name', 'delete transaction categories')->exists()) {
            Permission::create(['name' => 'delete transaction categories']);
        }

        // transactions
        if (!Permission::where('name', 'view transactions')->exists()) {
            Permission::create(['name' => 'view transactions']);
        }
        if (!Permission::where('name', 'create transactions')->exists()) {
            Permission::create(['name' => 'create transactions']);
        }
        if (!Permission::where('name', 'edit transactions')->exists()) {
            Permission::create(['name' => 'edit transactions']);
        }
        if (!Permission::where('name', 'delete transactions')->exists()) {
            Permission::create(['name' => 'delete transactions']);
        }

        // Create roles and assign permissions

        // SuperAdmin (Eng yuqori admin)
        if (!Role::where('name', 'superadmin')->exists()) {
            $superAdminRole = Role::create(['name' => 'superadmin']);
        } else {
            $superAdminRole = Role::where('name', 'superadmin')->first();
        }
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
            'delete companies',
            'view transactions',
            'edit transactions',
            'delete transactions'
        ]);

        // Admin (Korxona egasi)
        if (!Role::where('name', 'admin')->exists()) {
            $adminRole = Role::create(['name' => 'admin']);
        } else {
            $adminRole = Role::where('name', 'admin')->first();
        }
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
            'delete transaction categories',
            'view transactions',
            'create transactions',
            'edit transactions',
            'delete transactions'
        ]);

        // Manager (Ish boshqaruvchi)
        if (!Role::where('name', 'manager')->exists()) {
            $managerRole = Role::create(['name' => 'manager']);
        } else {
            $managerRole = Role::where('name', 'manager')->first();
        }
        $managerRole->givePermissionTo([
            'view planners',
            'create planners',
            'edit planners',
            'delete planners',
            'view employees',
            'create employees',
            'edit employees',
            'delete employees',
            'view transaction categories',
            'view transactions',
            'create transactions',
            'edit transactions',
            'delete transactions'
        ]);

        // Planner (Rejalashtiruvchi)
        if (!Role::where('name', 'planner')->exists()) {
            $plannerRole = Role::create(['name' => 'planner']);
        } else {
            $plannerRole = Role::where('name', 'planner')->first();
        }
        $plannerRole->givePermissionTo([
            'view employees',
            'create employees',
            'edit employees',
            'delete employees',
            'view transactions',
            'create transactions',
            'edit transactions',
            'delete transactions',
            'view transaction categories',
        ]);

        // Employee (Oddiy xodim)
        if (!Role::where('name', 'employee')->exists()) {
            $employeeRole = Role::create(['name' => 'employee']);
        } else {
            $employeeRole = Role::where('name', 'employee')->first();
        }
        $employeeRole->givePermissionTo([]);

        // Create default superadmin user
        if (!User::where('phone', '998000000000')->exists()) {
            $user = User::create([
                'phone' => '998000000000',
                'password' => bcrypt('password'),
            ]);
            $user->assignRole('superadmin');
        }
    }
}
