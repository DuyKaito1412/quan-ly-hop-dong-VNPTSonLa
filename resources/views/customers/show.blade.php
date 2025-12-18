@extends('layouts.app')

@section('title', 'Chi tiết Khách hàng')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-person"></i> Thông tin Khách hàng</h5>
                <div>
                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-pencil"></i> Sửa
                    </a>
                    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">Mã khách hàng:</th>
                        <td><strong>{{ $customer->code }}</strong></td>
                    </tr>
                    <tr>
                        <th>Tên khách hàng:</th>
                        <td>{{ $customer->name }}</td>
                    </tr>
                    <tr>
                        <th>Mã số thuế:</th>
                        <td>{{ $customer->tax_code ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>{{ $customer->email ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Điện thoại:</th>
                        <td>{{ $customer->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Địa chỉ:</th>
                        <td>{{ $customer->address ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Trạng thái:</th>
                        <td>
                            @if($customer->is_active)
                                <span class="badge bg-success">Hoạt động</span>
                            @else
                                <span class="badge bg-secondary">Không hoạt động</span>
                            @endif
                        </td>
                    </tr>
                    @if($customer->notes)
                    <tr>
                        <th>Ghi chú:</th>
                        <td>{{ $customer->notes }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Hợp đồng</h5>
            </div>
            <div class="card-body">
                <h3 class="text-primary">{{ $customer->contracts->count() }}</h3>
                <p class="text-muted mb-3">Tổng số hợp đồng</p>
                <a href="{{ route('contracts.index', ['customer_id' => $customer->id]) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye"></i> Xem danh sách
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

