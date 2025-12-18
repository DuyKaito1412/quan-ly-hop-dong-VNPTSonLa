<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['code' => 'KH001', 'name' => 'Công ty ABC', 'tax_code' => '0123456789', 'email' => 'contact@abc.com', 'phone' => '0241234567'],
            ['code' => 'KH002', 'name' => 'Công ty XYZ', 'tax_code' => '0123456790', 'email' => 'info@xyz.com', 'phone' => '0241234568'],
            ['code' => 'KH003', 'name' => 'Công ty DEF', 'tax_code' => '0123456791', 'email' => 'hello@def.com', 'phone' => '0241234569'],
            ['code' => 'KH004', 'name' => 'Công ty GHI', 'tax_code' => '0123456792', 'email' => 'contact@ghi.com', 'phone' => '0241234570'],
            ['code' => 'KH005', 'name' => 'Công ty JKL', 'tax_code' => '0123456793', 'email' => 'info@jkl.com', 'phone' => '0241234571'],
            ['code' => 'KH006', 'name' => 'Công ty MNO', 'tax_code' => '0123456794', 'email' => 'hello@mno.com', 'phone' => '0241234572'],
            ['code' => 'KH007', 'name' => 'Công ty PQR', 'tax_code' => '0123456795', 'email' => 'contact@pqr.com', 'phone' => '0241234573'],
            ['code' => 'KH008', 'name' => 'Công ty STU', 'tax_code' => '0123456796', 'email' => 'info@stu.com', 'phone' => '0241234574'],
            ['code' => 'KH009', 'name' => 'Công ty VWX', 'tax_code' => '0123456797', 'email' => 'hello@vwx.com', 'phone' => '0241234575'],
            ['code' => 'KH010', 'name' => 'Công ty YZA', 'tax_code' => '0123456798', 'email' => 'contact@yza.com', 'phone' => '0241234576'],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(['code' => $customer['code']], $customer);
        }
    }
}
