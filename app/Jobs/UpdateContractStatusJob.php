<?php

namespace App\Jobs;

use App\Models\Contract;
use App\Models\StatusHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;

class UpdateContractStatusJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $today = Carbon::today();
        $nearExpiryThreshold = config('contracts.near_expiry_days', 30);
        
        // Cập nhật hợp đồng hết hạn
        $expiredContracts = Contract::where('status', '!=', 'EXPIRED')
            ->where('status', '!=', 'TERMINATED')
            ->where('status', '!=', 'RENEWED')
            ->where('end_date', '<', $today)
            ->get();
        
        foreach ($expiredContracts as $contract) {
            $oldStatus = $contract->status;
            $contract->status = 'EXPIRED';
            $contract->save();
            
            // Ghi lịch sử
            StatusHistory::create([
                'contract_id' => $contract->id,
                'from_status' => $oldStatus,
                'to_status' => 'EXPIRED',
                'changed_at' => now(),
            ]);
        }
        
        // Cập nhật hợp đồng sắp hết hạn
        $nearExpiryDate = $today->copy()->addDays($nearExpiryThreshold);
        $nearExpiryContracts = Contract::where('status', 'ACTIVE')
            ->where('end_date', '<=', $nearExpiryDate)
            ->where('end_date', '>=', $today)
            ->get();
        
        foreach ($nearExpiryContracts as $contract) {
            $oldStatus = $contract->status;
            $contract->status = 'NEAR_EXPIRY';
            $contract->save();
            
            // Ghi lịch sử
            StatusHistory::create([
                'contract_id' => $contract->id,
                'from_status' => $oldStatus,
                'to_status' => 'NEAR_EXPIRY',
                'changed_at' => now(),
            ]);
        }
    }
}
