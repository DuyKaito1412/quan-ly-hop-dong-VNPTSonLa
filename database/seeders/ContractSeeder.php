<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\ContractItem;
use App\Models\Customer;
use App\Models\Milestone;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ContractSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::all();
        $services = Service::all();
        $salesPeople = User::where('is_sales_person', true)->get();

        if ($customers->isEmpty() || $services->isEmpty() || $salesPeople->isEmpty()) {
            return;
        }

        for ($i = 1; $i <= 20; $i++) {
            $customer = $customers->random();
            $salesPerson = $salesPeople->random();
            
            // Random end_date trong 90 ngày
            $endDate = Carbon::now()->addDays(rand(-30, 90));
            $startDate = $endDate->copy()->subMonths(rand(6, 24));
            
            $contract = Contract::create([
                'contract_no' => 'HD' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'customer_id' => $customer->id,
                'sales_person_id' => $salesPerson->id,
                'title' => "Hợp đồng {$customer->name} - {$i}",
                'description' => "Mô tả hợp đồng số {$i}",
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $this->determineStatus($endDate),
                'total_amount' => rand(50000000, 500000000),
                'currency' => 'VND',
                'created_by' => $salesPerson->id,
            ]);

            // Tạo contract items
            $service = $services->random();
            ContractItem::create([
                'contract_id' => $contract->id,
                'service_id' => $service->id,
                'quantity' => rand(1, 10),
                'unit_price' => $service->default_price ?? rand(1000000, 50000000),
                'amount' => rand(10000000, 200000000),
                'cycle' => ['MONTHLY', 'QUARTERLY', 'YEARLY', 'ONE_TIME'][rand(0, 3)],
            ]);

            // Tạo milestone EXPIRY
            Milestone::create([
                'contract_id' => $contract->id,
                'type' => 'EXPIRY',
                'title' => 'Hết hạn hợp đồng',
                'due_date' => $endDate,
                'done' => false,
            ]);
        }
    }

    private function determineStatus($endDate): string
    {
        $daysUntilExpiry = Carbon::now()->diffInDays($endDate, false);
        
        if ($daysUntilExpiry < 0) {
            return 'EXPIRED';
        } elseif ($daysUntilExpiry <= 30) {
            return 'NEAR_EXPIRY';
        } else {
            return 'ACTIVE';
        }
    }
}
