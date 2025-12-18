<?php

namespace Database\Seeders;

use App\Models\Solution;
use Illuminate\Database\Seeder;

class SolutionSeeder extends Seeder
{
    public function run(): void
    {
        $solutions = [
            ['code' => 'DGS',   'name' => 'TT Giải pháp Chính phủ số'],
            ['code' => 'GEOIT', 'name' => 'TT Giải pháp Đất đai & TNMT'],
            ['code' => 'DES',   'name' => 'TT Giải pháp Giáo dục số'],
            ['code' => 'DAS',   'name' => 'TT Giải pháp Hành chính số'],
            ['code' => 'NNS',   'name' => 'TT Giải pháp Nông nghiệp số'],
            ['code' => 'SI',    'name' => 'TT Giải pháp Số doanh nghiệp'],
            ['code' => 'DMS',   'name' => 'TT Giải pháp Y tế số'],
            ['code' => 'IC',    'name' => 'TT Sáng tạo'],
            ['code' => 'ATTT',  'name' => 'TT VNPT ATTT'],
            ['code' => 'UNCAT', 'name' => 'Chưa phân loại'],
        ];

        foreach ($solutions as $data) {
            $code = strtoupper($data['code']);

            Solution::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $data['name'],
                    'description' => $data['description'] ?? null,
                    'is_active' => true,
                ]
            );
        }
    }
}


