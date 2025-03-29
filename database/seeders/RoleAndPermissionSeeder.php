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

        // only superadmin and admin and manager
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

        // only admin
        if (!Permission::where('name', 'balance-transfer')->exists()) {
            Permission::create(['name' => 'balance-transfer']);
        }

        // Create roles and assign permissions
        // Superadmin
        if (!Role::where('name', 'superadmin')->exists()) {
            $superadminRole = Role::create(['name' => 'superadmin']);
        } else {
            $superadminRole = Role::where('name', 'superadmin')->first();
        }
        $superadminRole->givePermissionTo(Permission::all());

        // Admin
        if (!Role::where('name', 'admin')->exists()) {
            $adminRole = Role::create(['name' => 'admin']);
        } else {
            $adminRole = Role::where('name', 'admin')->first();
        }
        $adminRole->givePermissionTo([
            'view employees',
            'create employees',
            'edit employees',
            'delete employees',
            'view transaction categories',
            'view transactions',
            'create transactions',
            'edit transactions',
            'delete transactions',
            'balance-transfer'
        ]);

        // Manager
        if (!Role::where('name', 'manager')->exists()) {
            $managerRole = Role::create(['name' => 'manager']);
        } else {
            $managerRole = Role::where('name', 'manager')->first();
        }
        $managerRole->givePermissionTo([
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

        // Planner
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

        // Employee
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
