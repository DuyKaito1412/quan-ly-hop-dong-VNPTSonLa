<?php

namespace App\Jobs;

use App\Models\Contract;
use App\Models\Milestone;
use App\Models\Reminder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;

class CreateRemindersJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $reminderDays = [30, 15, 7, 3, 1];
        $today = Carbon::today();
        
        // Lấy các milestone EXPIRY chưa done
        $milestones = Milestone::where('type', 'EXPIRY')
            ->where('done', false)
            ->where('due_date', '>=', $today)
            ->with('contract')
            ->get();
        
        foreach ($milestones as $milestone) {
            $contract = $milestone->contract;
            if (!$contract || !$contract->sales_person_id) {
                continue;
            }
            
            $endDate = Carbon::parse($milestone->due_date);
            
            foreach ($reminderDays as $days) {
                $remindDate = $endDate->copy()->subDays($days);
                
                // Chỉ tạo reminder nếu ngày nhắc nhở chưa qua và chưa có reminder
                if ($remindDate->gte($today)) {
                    $existingReminder = Reminder::where('contract_id', $contract->id)
                        ->where('milestone_id', $milestone->id)
                        ->where('remind_before_days', $days)
                        ->first();
                    
                    if (!$existingReminder) {
                        Reminder::create([
                            'contract_id' => $contract->id,
                            'milestone_id' => $milestone->id,
                            'recipient_user_id' => $contract->sales_person_id,
                            'remind_before_days' => $days,
                            'remind_date' => $remindDate,
                            'status' => 'PENDING',
                            'message' => "Hợp đồng {$contract->contract_no} sẽ hết hạn sau {$days} ngày ({$endDate->format('d/m/Y')})",
                        ]);
                    }
                }
            }
        }
    }
}
