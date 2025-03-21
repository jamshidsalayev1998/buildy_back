<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use App\Models\Admin;
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

            // Admin uchun login ma'lumotlari
            $phone = '99890' . str_pad($index + 1, 7, '0', STR_PAD_LEFT);
            $password = 'password123'; // Default parol

            // User yaratamiz
            $user = User::create([
                'phone' => $phone,
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

            // Login ma'lumotlarini faylga yozish
            $credentials = sprintf(
                "Kompaniya: %s\nTelefon: %s\nParol: %s\n------------------------\n",
                $company->name,
                $phone,
                $password
            );
            File::append($logPath, $credentials);

            // Xavfsizlik uchun logga ham yozamiz
            Log::info('Admin yaratildi', [
                'company' => $company->name,
                'phone' => $phone,
                'password' => $password
            ]);
        }

        $this->command->info('Admin login malumotlari storage/logs/admin_credentials.txt faylida saqlandi');
    }
}