@extends('layouts.app')

@section('title', 'Chi tiết Dịch vụ')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-briefcase"></i> Thông tin Dịch vụ</h5>
        <div>
            <a href="{{ route('services.edit', $service) }}" class="btn btn-sm btn-primary">
                <i class="bi bi-pencil"></i> Sửa
            </a>
            <a href="{{ route('services.index') }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <th width="200">Mã dịch vụ:</th>
                <td><strong>{{ $service->code }}</strong></td>
            </tr>
            <tr>
                <th>Tên dịch vụ:</th>
                <td>{{ $service->name }}</td>
            </tr>
            <tr>
                <th>Giải pháp:</th>
                <td>{{ $service->solution->name ?? 'Chưa phân loại' }}</td>
            </tr>
            <tr>
                <th>Đơn vị tính:</th>
                <td>{{ $service->unit ?? '-' }}</td>
            </tr>
            <tr>
                <th>Giá mặc định:</th>
                <td>{{ $service->default_price ? number_format($service->default_price, 0, ',', '.') . ' VNĐ' : '-' }}</td>
            </tr>
            <tr>
                <th>Trạng thái:</th>
                <td>
                    @if($service->is_active)
                        <span class="badge bg-success">Hoạt động</span>
                    @else
                        <span class="badge bg-secondary">Không hoạt động</span>
                    @endif
                </td>
            </tr>
            @if($service->description)
            <tr>
                <th>Mô tả:</th>
                <td>{{ $service->description }}</td>
            </tr>
            @endif
        </table>
    </div>
</div>
@endsection

