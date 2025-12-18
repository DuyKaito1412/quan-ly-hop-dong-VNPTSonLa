@extends('layouts.app')

@section('title', 'Báo cáo Hợp đồng đến hạn')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-graph-up"></i> Báo cáo Hợp đồng đến hạn trong tháng</h5>
        <div>
            <form method="GET" action="{{ route('reports.expiry') }}" class="d-inline">
                <div class="input-group input-group-sm">
                    <select name="month" class="form-select" style="width: auto;">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('month', now()->month) == $i ? 'selected' : '' }}>
                                Tháng {{ $i }}
                            </option>
                        @endfor
                    </select>
                    <select name="year" class="form-select" style="width: auto;">
                        @for($i = now()->year - 2; $i <= now()->year + 2; $i++)
                            <option value="{{ $i }}" {{ request('year', now()->year) == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                    <select name="solution_id" class="form-select" style="width: auto;">
                        <option value="">Tất cả Giải pháp</option>
                        @foreach($solutions as $solution)
                            <option value="{{ $solution->id }}" {{ request('solution_id') == $solution->id ? 'selected' : '' }}>
                                {{ $solution->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-outline-primary">Xem</button>
                </div>
            </form>
            <a href="{{ route('reports.expiry.export', ['month' => request('month', now()->month), 'year' => request('year', now()->year), 'solution_id' => request('solution_id')]) }}" class="btn btn-success btn-sm ms-2">
                <i class="bi bi-download"></i> Xuất Excel
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Số HĐ</th>
                        <th>Khách hàng</th>
                        <th>NVKD</th>
                        <th>Tiêu đề</th>
                        <th>Giải pháp (đầu tiên)</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contracts as $contract)
                        <tr>
                            <td><strong>{{ $contract->contract_no }}</strong></td>
                            <td>{{ $contract->customer->name }}</td>
                            <td>{{ $contract->salesPerson->name ?? 'N/A' }}</td>
                            <td>{{ Str::limit($contract->title, 30) }}</td>
                            <td>
                                @php
                                    $firstItem = $contract->items->first();
                                @endphp
                                {{ $firstItem?->service->solution->name ?? 'N/A' }}
                            </td>
                            <td>{{ $contract->start_date->format('d/m/Y') }}</td>
                            <td>{{ $contract->end_date->format('d/m/Y') }}</td>
                            <td>{{ number_format($contract->total_amount, 0, ',', '.') }} {{ $contract->currency }}</td>
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
                                <a href="{{ route('contracts.show', $contract) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0">Không có hợp đồng nào đến hạn trong tháng này</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            <strong>Tổng số: {{ $contracts->count() }} hợp đồng</strong>
        </div>
    </div>
</div>
@endsection

