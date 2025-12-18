<?php

namespace Database\Seeders;

use App\Models\Solution;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $uncat = Solution::where('code', 'UNCAT')->first();

        $services = [
            ['code' => 'DV001', 'name' => 'Phát triển Website', 'unit' => 'project', 'default_price' => 50000000, 'description' => 'Phát triển website doanh nghiệp'],
            ['code' => 'DV002', 'name' => 'Phát triển Mobile App', 'unit' => 'project', 'default_price' => 100000000, 'description' => 'Phát triển ứng dụng mobile'],
            ['code' => 'DV003', 'name' => 'Bảo trì hệ thống', 'unit' => 'month', 'default_price' => 10000000, 'description' => 'Bảo trì hệ thống hàng tháng'],
            ['code' => 'DV004', 'name' => 'Tư vấn CNTT', 'unit' => 'hour', 'default_price' => 500000, 'description' => 'Tư vấn công nghệ thông tin'],
            ['code' => 'DV005', 'name' => 'Cloud Hosting', 'unit' => 'month', 'default_price' => 2000000, 'description' => 'Dịch vụ cloud hosting'],
        ];

        foreach ($services as $service) {
            $data = $service;
            if ($uncat) {
                $data['solution_id'] = $uncat->id;
            }

            Service::firstOrCreate(['code' => $service['code']], $data);
        }
    }
}
