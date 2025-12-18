@extends('layouts.app')

@section('title', 'Quản lý Hợp đồng')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Danh sách Hợp đồng</h5>
        <div>
            <a href="{{ route('contracts.import.show') }}" class="btn btn-success btn-sm me-2">
                <i class="bi bi-upload"></i> Import Excel
            </a>
            <a href="{{ route('contracts.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Thêm mới
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('contracts.index') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="customer_id" class="form-select">
                        <option value="">Tất cả KH</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="sales_person_id" class="form-select">
                        <option value="">Tất cả NVKD</option>
                        @foreach($salesPeople as $person)
                            <option value="{{ $person->id }}" {{ request('sales_person_id') == $person->id ? 'selected' : '' }}>
                                {{ $person->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="DRAFT" {{ request('status') == 'DRAFT' ? 'selected' : '' }}>Draft</option>
                        <option value="ACTIVE" {{ request('status') == 'ACTIVE' ? 'selected' : '' }}>Active</option>
                        <option value="NEAR_EXPIRY" {{ request('status') == 'NEAR_EXPIRY' ? 'selected' : '' }}>Sắp hết hạn</option>
                        <option value="EXPIRED" {{ request('status') == 'EXPIRED' ? 'selected' : '' }}>Hết hạn</option>
                        <option value="RENEWED" {{ request('status') == 'RENEWED' ? 'selected' : '' }}>Gia hạn</option>
                        <option value="TERMINATED" {{ request('status') == 'TERMINATED' ? 'selected' : '' }}>Chấm dứt</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="btn-group w-100">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-search"></i> Tìm kiếm
                        </button>
                        <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Số HĐ</th>
                        <th>Khách hàng</th>
                        <th>NVKD</th>
                        <th>Tiêu đề</th>
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
                            <td>{{ $contract->start_date->format('d/m/Y') }}</td>
                            <td>
                                @php
                                    $daysLeft = now()->diffInDays($contract->end_date, false);
                                @endphp
                                <span class="{{ $daysLeft <= 30 ? 'text-danger fw-bold' : '' }}">
                                    {{ $contract->end_date->format('d/m/Y') }}
                                </span>
                            </td>
                            <td>{{ number_format($contract->total_amount, 0, ',', '.') }} {{ $contract->currency }}</td>
                            <td>
                                @if($contract->status == 'ACTIVE')
                                    <span class="badge bg-success">Active</span>
                                @elseif($contract->status == 'NEAR_EXPIRY')
                                    <span class="badge bg-warning">Sắp hết hạn</span>
                                @elseif($contract->status == 'EXPIRED')
                                    <span class="badge bg-danger">Hết hạn</span>
                                @elseif($contract->status == 'DRAFT')
                                    <span class="badge bg-secondary">Draft</span>
                                @elseif($contract->status == 'RENEWED')
                                    <span class="badge bg-info">Gia hạn</span>
                                @else
                                    <span class="badge bg-dark">Chấm dứt</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('contracts.show', $contract) }}" class="btn btn-outline-info" title="Xem">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-outline-primary" title="Sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0">Không có hợp đồng nào</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $contracts->links() }}
        </div>
    </div>
</div>
@endsection

