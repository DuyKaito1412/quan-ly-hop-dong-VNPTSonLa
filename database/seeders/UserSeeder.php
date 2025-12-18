<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@local'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'employee_code' => 'ADM001',
                'is_sales_person' => false,
            ]
        );
        $admin->assignRole('ADMIN');

        // Manager
        $manager = User::firstOrCreate(
            ['email' => 'manager@local'],
            [
                'name' => 'Manager',
                'password' => Hash::make('password'),
                'employee_code' => 'MGR001',
                'is_sales_person' => false,
            ]
        );
        $manager->assignRole('MANAGER');

        // Sales
        $sales1 = User::firstOrCreate(
            ['email' => 'sales1@local'],
            [
                'name' => 'Sales Person 1',
                'password' => Hash::make('password'),
                'employee_code' => 'SAL001',
                'phone' => '0901234567',
                'is_sales_person' => true,
            ]
        );
        $sales1->assignRole('SALES');

        // Tạo thêm một số sales khác
        for ($i = 2; $i <= 3; $i++) {
            $sales = User::firstOrCreate(
                ['email' => "sales{$i}@local"],
                [
                    'name' => "Sales Person {$i}",
                    'password' => Hash::make('password'),
                    'employee_code' => "SAL00{$i}",
                    'phone' => "090123456{$i}",
                    'is_sales_person' => true,
                ]
            );
            $sales->assignRole('SALES');
        }
    }
}
