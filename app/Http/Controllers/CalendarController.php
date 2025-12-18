<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Milestone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calendar.index');
    }

    public function events(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        
        $user = Auth::user();
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        
        // Lấy milestones EXPIRY trong tháng
        $milestoneQuery = Milestone::where('type', 'EXPIRY')
            ->whereBetween('due_date', [$startDate, $endDate])
            ->with('contract');
        
        // Phân quyền
        if ($user->hasRole('SALES')) {
            $milestoneQuery->whereHas('contract', function($q) use ($user) {
                $q->where('sales_person_id', $user->id);
            });
        } elseif (!$user->hasAnyRole(['ADMIN', 'MANAGER'])) {
            $milestoneQuery->whereRaw('1 = 0');
        }
        
        $milestones = $milestoneQuery->get();
        
        $events = [];
        foreach ($milestones as $milestone) {
            $contract = $milestone->contract;
            if ($contract) {
                $events[] = [
                    'title' => $contract->contract_no . ' - ' . $contract->title,
                    'start' => $milestone->due_date->format('Y-m-d'),
                    'url' => route('contracts.show', $contract->id),
                    'backgroundColor' => $this->getEventColor($milestone->due_date),
                ];
            }
        }
        
        return response()->json($events);
    }

    private function getEventColor($dueDate)
    {
        $daysUntilExpiry = Carbon::now()->diffInDays($dueDate, false);
        
        if ($daysUntilExpiry < 0) {
            return '#dc3545'; // Red - Expired
        } elseif ($daysUntilExpiry <= 7) {
            return '#ffc107'; // Yellow - Urgent
        } elseif ($daysUntilExpiry <= 30) {
            return '#fd7e14'; // Orange - Near expiry
        } else {
            return '#28a745'; // Green - Active
        }
    }
}
