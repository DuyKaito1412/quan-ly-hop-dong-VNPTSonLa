<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Milestone;
use App\Models\Solution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function expiry(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        
        $user = Auth::user();
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        
        $query = Contract::whereBetween('end_date', [$startDate, $endDate])
            ->with(['customer', 'salesPerson', 'items.service.solution']);
        
        // Phân quyền
        if ($user->hasRole('SALES')) {
            $query->where('sales_person_id', $user->id);
        } elseif (!$user->hasAnyRole(['ADMIN', 'MANAGER'])) {
            $query->whereRaw('1 = 0');
        }

        if ($request->filled('solution_id')) {
            $solutionId = $request->solution_id;
            $query->whereHas('items.service', function ($q) use ($solutionId) {
                $q->where('solution_id', $solutionId);
            });
        }
        
        $contracts = $query->orderBy('end_date')->get();
        $solutions = Solution::orderBy('name')->get();
        
        return view('reports.expiry', compact('contracts', 'month', 'year', 'solutions'));
    }

    public function export(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        
        $user = Auth::user();
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        
        $query = Contract::whereBetween('end_date', [$startDate, $endDate])
            ->with(['customer', 'salesPerson', 'items.service.solution']);
        
        // Phân quyền
        if ($user->hasRole('SALES')) {
            $query->where('sales_person_id', $user->id);
        } elseif (!$user->hasAnyRole(['ADMIN', 'MANAGER'])) {
            $query->whereRaw('1 = 0');
        }

        if ($request->filled('solution_id')) {
            $solutionId = $request->solution_id;
            $query->whereHas('items.service', function ($q) use ($solutionId) {
                $q->where('solution_id', $solutionId);
            });
        }
        
        $contracts = $query->orderBy('end_date')->get();
        
        $data = [];
        $data[] = ['Số HĐ', 'Khách hàng', 'NVKD', 'Giải pháp (đầu tiên)', 'Tiêu đề', 'Ngày bắt đầu', 'Ngày kết thúc', 'Trạng thái', 'Tổng tiền'];
        
        foreach ($contracts as $contract) {
            $firstItem = $contract->items->first();
            $firstSolutionName = $firstItem?->service->solution->name ?? '';

            $data[] = [
                $contract->contract_no,
                $contract->customer->name ?? '',
                $contract->salesPerson->name ?? '',
                $firstSolutionName,
                $contract->title,
        }
        
        $export = new class($data) implements \Maatwebsite\Excel\Concerns\FromArray {
            public function __construct(public array $data) {}
            public function array(): array { return $this->data; }
        };
        
        return Excel::download($export, "hop_dong_den_han_{$month}_{$year}.xlsx");
    }
}
