@extends('layouts.app')

@section('title', 'Quản lý Dịch vụ')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-briefcase"></i> Danh sách Dịch vụ</h5>
        <a href="{{ route('services.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Thêm mới
        </a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('services.index') }}" class="mb-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Tìm kiếm</label>
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên, mã..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Giải pháp</label>
                    <select name="solution_id" class="form-select">
                        <option value="">-- Tất cả giải pháp --</option>
                        @foreach($solutions as $solution)
                            <option value="{{ $solution->id }}" {{ request('solution_id') == $solution->id ? 'selected' : '' }}>
                                {{ $solution->name }} ({{ $solution->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Trạng thái</label>
                    <select name="is_active" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Không hoạt động</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" value="1" id="group_by_solution" name="group_by_solution" {{ request('group_by_solution') ? 'checked' : '' }}>
                        <label class="form-check-label" for="group_by_solution">
                            Gom theo Giải pháp
                        </label>
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </form>

        @if($isGrouped)
            @php
                $grouped = $services->groupBy(function ($service) {
                    return optional($service->solution)->name ?? 'Chưa phân loại';
                });
            @endphp

            @forelse($grouped as $solutionName => $groupServices)
                <h6 class="mt-4 mb-2">
                    <i class="bi bi-layers"></i> {{ $solutionName }}
                </h6>
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 120px;">Mã</th>
                                <th>Tên dịch vụ</th>
                                <th style="width: 120px;">Đơn vị</th>
                                <th style="width: 160px;">Giá mặc định</th>
                                <th style="width: 100px;">Trạng thái</th>
                                <th style="width: 140px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupServices as $service)
                                <tr>
                                    <td><strong>{{ $service->code }}</strong></td>
                                    <td>{{ $service->name }}</td>
                                    <td>{{ $service->unit ?? '-' }}</td>
                                    <td>{{ $service->default_price ? number_format($service->default_price, 0, ',', '.') . ' VNĐ' : '-' }}</td>
                                    <td>
                                        @if($service->is_active)
                                            <span class="badge bg-success">Hoạt động</span>
                                        @else
                                            <span class="badge bg-secondary">Không hoạt động</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('services.show', $service) }}" class="btn btn-outline-info" title="Xem">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('services.edit', $service) }}" class="btn btn-outline-primary" title="Sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('services.destroy', $service) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa dịch vụ này?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Xóa">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @empty
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">Không có dịch vụ nào</p>
                </div>
            @endforelse
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Mã</th>
                            <th>Tên dịch vụ</th>
                            <th>Giải pháp</th>
                            <th>Đơn vị</th>
                            <th>Giá mặc định</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                            <tr>
                                <td><strong>{{ $service->code }}</strong></td>
                                <td>{{ $service->name }}</td>
                                <td>{{ $service->solution->name ?? 'Chưa phân loại' }}</td>
                                <td>{{ $service->unit ?? '-' }}</td>
                                <td>{{ $service->default_price ? number_format($service->default_price, 0, ',', '.') . ' VNĐ' : '-' }}</td>
                                <td>
                                    @if($service->is_active)
                                        <span class="badge bg-success">Hoạt động</span>
                                    @else
                                        <span class="badge bg-secondary">Không hoạt động</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('services.show', $service) }}" class="btn btn-outline-info" title="Xem">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('services.edit', $service) }}" class="btn btn-outline-primary" title="Sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('services.destroy', $service) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa dịch vụ này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                    <p class="mt-2 mb-0">Không có dịch vụ nào</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $services->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

