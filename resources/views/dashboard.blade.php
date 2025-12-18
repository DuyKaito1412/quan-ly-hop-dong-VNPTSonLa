@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-1">Hợp đồng Active</h5>
                        <h2 class="mb-0">{{ $stats['active'] }}</h2>
                    </div>
                    <i class="bi bi-check-circle" style="font-size: 3rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-1">Sắp hết hạn</h5>
                        <h2 class="mb-0">{{ $stats['near_expiry'] }}</h2>
                    </div>
                    <i class="bi bi-exclamation-triangle" style="font-size: 3rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-1">Đã hết hạn</h5>
                        <h2 class="mb-0">{{ $stats['expired'] }}</h2>
                    </div>
                    <i class="bi bi-x-circle" style="font-size: 3rem; opacity: 0.5;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Hợp đồng sắp hết hạn trong 30 ngày</h5>
        <a href="{{ route('contracts.index', ['status' => 'NEAR_EXPIRY']) }}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Số HĐ</th>
                        <th>Khách hàng</th>
                        <th>NVKD</th>
                        <th>Ngày hết hạn</th>
                        <th>Còn lại</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nearExpiryContracts as $contract)
                        <tr>
                            <td><strong>{{ $contract->contract_no }}</strong></td>
                            <td>{{ $contract->customer->name }}</td>
                            <td>{{ $contract->salesPerson->name ?? 'N/A' }}</td>
                            <td>{{ $contract->end_date->format('d/m/Y') }}</td>
                            <td>
                                @php
                                    $daysLeft = now()->diffInDays($contract->end_date, false);
                                @endphp
                                @if($daysLeft < 0)
                                    <span class="badge bg-danger">Đã hết hạn {{ abs($daysLeft) }} ngày</span>
                                @elseif($daysLeft <= 7)
                                    <span class="badge bg-danger">{{ $daysLeft }} ngày</span>
                                @elseif($daysLeft <= 30)
                                    <span class="badge bg-warning">{{ $daysLeft }} ngày</span>
                                @else
                                    <span class="badge bg-success">{{ $daysLeft }} ngày</span>
                                @endif
                            </td>
                            <td>
                                @if($contract->status == 'ACTIVE')
                                    <span class="badge bg-success">Active</span>
                                @elseif($contract->status == 'NEAR_EXPIRY')
                                    <span class="badge bg-warning">Sắp hết hạn</span>
                                @elseif($contract->status == 'EXPIRED')
                                    <span class="badge bg-danger">Hết hạn</span>
                                @else
                                    <span class="badge bg-secondary">{{ $contract->status }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('contracts.show', $contract) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> Xem
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0">Không có hợp đồng nào sắp hết hạn</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
