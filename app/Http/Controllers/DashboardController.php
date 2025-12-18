<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Lấy query base theo quyền
        $contractQuery = Contract::query();
        
        if ($user->hasRole('SALES')) {
            $contractQuery->where('sales_person_id', $user->id);
        } elseif (!$user->hasAnyRole(['ADMIN', 'MANAGER'])) {
            // Các role khác không thấy gì
            $contractQuery->whereRaw('1 = 0');
        }
        
        // Thống kê
        $stats = [
            'active' => (clone $contractQuery)->where('status', 'ACTIVE')->count(),
            'near_expiry' => (clone $contractQuery)->where('status', 'NEAR_EXPIRY')->count(),
            'expired' => (clone $contractQuery)->where('status', 'EXPIRED')->count(),
        ];
        
        // Hợp đồng sắp hết hạn trong 30 ngày
        $nearExpiryContracts = (clone $contractQuery)
            ->where('end_date', '<=', now()->addDays(30))
            ->where('end_date', '>=', now())
            ->whereIn('status', ['ACTIVE', 'NEAR_EXPIRY'])
            ->with(['customer', 'salesPerson'])
            ->orderBy('end_date')
            ->limit(10)
            ->get();
        
        return view('dashboard', compact('stats', 'nearExpiryContracts'));
    }
}
