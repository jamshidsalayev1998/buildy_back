<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use App\Models\Admin;
use App\Models\Employee;
use App\Models\TransactionCategory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class CompanyAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Admin credentials faylini yaratish uchun yo'l
        $logPath = storage_path('logs/admin_credentials.txt');

        // Eski faylni o'chirish
        if (File::exists($logPath)) {
            File::delete($logPath);
        }

        // Fayl yaratish va sarlavha yozish
        File::put($logPath, "ADMIN LOGIN MA'LUMOTLARI\n");
        File::append($logPath, "========================\n\n");

        $companies = [
            [
                'name' => 'ABC Company',
                'address' => 'Toshkent sh., Chilonzor tumani',
                'phone' => '+998901234567',
                'email' => 'abc@company.uz',
                'description' => 'ABC kompaniyasi tavsifi',
                'status' => true
            ],
            [
                'name' => 'XYZ Industries',
                'address' => 'Toshkent sh., Yunusobod tumani',
                'phone' => '+998901234568',
                'email' => 'xyz@company.uz',
                'description' => 'XYZ kompaniyasi tavsifi',
                'status' => true
            ],
            [
                'name' => 'Tech Solutions',
                'address' => 'Toshkent sh., Mirzo Ulug\'bek tumani',
                'phone' => '+998901234569',
                'email' => 'tech@company.uz',
                'description' => 'Tech Solutions kompaniyasi tavsifi',
                'status' => true
            ]
        ];

        foreach ($companies as $index => $companyData) {
            if(Company::where('name', $companyData['name'])->exists()) {
                continue;
            }
            if(User::where('phone', $companyData['phone'])->exists()) {
                continue;
            }
            // Kompaniyani yaratamiz
            $company = Company::create($companyData);

            // Random telefon raqami generatsiya qilish uchun funksiya
            $generatePhone = function() {
                $operators = ['90', '91', '93', '94', '95', '97', '98', '99'];
                $operator = $operators[array_rand($operators)];
                $number = rand(1000000, 9999999);
                return '998' . $operator . $number;
            };
            $password = 'password123';

            // Admin uchun
            $adminPhone = $generatePhone();
            $user = User::create([
                'phone' => $adminPhone,
                'password' => Hash::make($password),
            ]);

            // Manager uchun
            $managerPhone = $generatePhone();
            $userEmployeeManager = User::create([
                'phone' => $managerPhone,
                'password' => Hash::make($password),
            ]);

            // Planner uchun
            $plannerPhone = $generatePhone();
            $userEmployeePlanner = User::create([
                'phone' => $plannerPhone,
                'password' => Hash::make($password),
            ]);

            // Employee uchun
            $employeePhone = $generatePhone();
            $userEmployeeEmployee = User::create([
                'phone' => $employeePhone,
                'password' => Hash::make($password),
            ]);

            // Userga admin rolini beramiz
            $user->assignRole('admin');

            // Admin yaratamiz
            $admin = Admin::create([
                'user_id' => $user->id,
                'company_id' => $company->id,
                'first_name' => 'Admin',
                'last_name' => $company->name,
                'middle_name' => 'Test',
                'gender' => ['male', 'female'][rand(0, 1)],
                'passport_number' => 'AA' . rand(1000000, 9999999),
                'birth_date' => '1990-01-01',
                'position' => 'Direktor',
                'work_type' => 'fixed',
                'hourly_rate' => 50000,
                'monthly_salary' => 5000000,
                'status' => 'active',
                'notes' => 'Test admin'
            ]);

            $transactionCategory = TransactionCategory::create([
                'name' => 'Test Transaction Category expense ' . $company->id,
                'company_id' => $company->id,
                'type' => 'EXPENSE'
            ]);
            $transactionCategory2 = TransactionCategory::create([
                'name' => 'Test Transaction Category income ' . $company->id,
                'company_id' => $company->id,
                'type' => 'INCOME'
            ]);

            $userEmployeeManager->assignRole('manager');

            $userEmployeePlanner->assignRole('planner');

            $userEmployeeEmployee->assignRole('employee');

            $employeeManager = Employee::create([
                'user_id' => $userEmployeeManager->id,
                'company_id' => $company->id,
                'first_name' => 'Manager_' . $company->name,
                'last_name' => $company->name,
                'middle_name' => 'Test',
                'gender' => ['male', 'female'][rand(0, 1)],
                'passport_number' => 'AA' . rand(1000000, 9999999),
                'birth_date' => '1990-01-01',
                'position' => 'manager',
                'work_type' => 'fixed',
                'monthly_salary' => 5000000,
                'status' => 'active',
                'notes' => 'Test manager',
                'created_by' => $admin->id,
            ]);

            $employeePlanner = Employee::create([
                'user_id' => $userEmployeePlanner->id,
                'company_id' => $company->id,
                'first_name' => 'Planner_' . $company->name,
                'last_name' => $company->name,
                'middle_name' => 'Test',
                'gender' => ['male', 'female'][rand(0, 1)],
                'passport_number' => 'AA' . rand(1000000, 9999999),
                'birth_date' => '1990-01-01',
                'position' => 'planner',
                'work_type' => 'fixed',
                'monthly_salary' => 5000000,
                'status' => 'active',
                'notes' => 'Test planner',
                'created_by' => $admin->id,
            ]);

            $employeeEmployee = Employee::create([
                'user_id' => $userEmployeeEmployee->id,
                'company_id' => $company->id,
                'first_name' => 'Employee_' . $company->name,
                'last_name' => $company->name,
                'middle_name' => 'Test',
                'gender' => ['male', 'female'][rand(0, 1)],
                'passport_number' => 'AA' . rand(1000000, 9999999),
                'birth_date' => '1990-01-01',
                'position' => 'employee',
                'work_type' => 'hourly',
                'hourly_rate' => 50000,
                'status' => 'active',
                'notes' => 'Test employee',
                'created_by' => $admin->id,
            ]);


            // Login ma'lumotlarini faylga yozish
            $credentials = sprintf(
                "Kompaniya: %s\n\n" .
                "ADMIN\n" .
                "Telefon: %s\n" .
                "Parol: %s\n\n" .
                "MANAGER\n" .
                "Telefon: %s\n" .
                "Parol: %s\n\n" .
                "PLANNER\n" .
                "Telefon: %s\n" .
                "Parol: %s\n\n" .
                "EMPLOYEE\n" .
                "Telefon: %s\n" .
                "Parol: %s\n" .
                "------------------------\n",
                $company->name,
                $adminPhone, $password, // Admin
                $managerPhone, $password, // Manager
                $plannerPhone, $password, // Planner
                $employeePhone, $password  // Employee
            );

            File::append($logPath, $credentials);

            // Xavfsizlik uchun logga ham yozamiz
            Log::info('Admin yaratildi', [
                'company' => $company->name,
                'phone' => $adminPhone,
                'password' => $password
            ]);
        }

        $this->command->info('Admin login malumotlari storage/logs/admin_credentials.txt faylida saqlandi');
    }
}
