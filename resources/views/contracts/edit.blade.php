@extends('layouts.app')

@section('title', 'Sửa Hợp đồng')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-pencil"></i> Sửa Hợp đồng: {{ $contract->contract_no }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('contracts.update', $contract) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="contract_no" class="form-label">Số hợp đồng <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('contract_no') is-invalid @enderror" id="contract_no" name="contract_no" value="{{ old('contract_no', $contract->contract_no) }}" required>
                    @error('contract_no')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="customer_id" class="form-label">Khách hàng <span class="text-danger">*</span></label>
                    <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id', $contract->customer_id) == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} ({{ $customer->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="sales_person_id" class="form-label">Nhân viên kinh doanh</label>
                    <select class="form-select" id="sales_person_id" name="sales_person_id">
                        <option value="">Chọn NVKD</option>
                        @foreach($salesPeople as $person)
                            <option value="{{ $person->id }}" {{ old('sales_person_id', $contract->sales_person_id) == $person->id ? 'selected' : '' }}>
                                {{ $person->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="status" name="status">
                        <option value="DRAFT" {{ old('status', $contract->status) == 'DRAFT' ? 'selected' : '' }}>Draft</option>
                        <option value="ACTIVE" {{ old('status', $contract->status) == 'ACTIVE' ? 'selected' : '' }}>Active</option>
                        <option value="NEAR_EXPIRY" {{ old('status', $contract->status) == 'NEAR_EXPIRY' ? 'selected' : '' }}>Sắp hết hạn</option>
                        <option value="EXPIRED" {{ old('status', $contract->status) == 'EXPIRED' ? 'selected' : '' }}>Hết hạn</option>
                        <option value="RENEWED" {{ old('status', $contract->status) == 'RENEWED' ? 'selected' : '' }}>Gia hạn</option>
                        <option value="TERMINATED" {{ old('status', $contract->status) == 'TERMINATED' ? 'selected' : '' }}>Chấm dứt</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $contract->title) }}" required>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="start_date" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', $contract->start_date->format('Y-m-d')) }}" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="end_date" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date', $contract->end_date->format('Y-m-d')) }}" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $contract->description) }}</textarea>
            </div>
            
            <div class="mb-3">
                <label for="total_amount" class="form-label">Tổng tiền</label>
                <input type="number" class="form-control" id="total_amount" name="total_amount" value="{{ old('total_amount', $contract->total_amount) }}">
            </div>
            
            <div class="mb-3">
                <label for="currency" class="form-label">Loại tiền</label>
                <select class="form-select" id="currency" name="currency">
                    <option value="VND" {{ old('currency', $contract->currency) == 'VND' ? 'selected' : '' }}>VND</option>
                    <option value="USD" {{ old('currency', $contract->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="terms" class="form-label">Điều khoản</label>
                <textarea class="form-control" id="terms" name="terms" rows="4">{{ old('terms', $contract->terms) }}</textarea>
            </div>
            
            <div class="mb-3">
                <label for="notes" class="form-label">Ghi chú</label>
                <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $contract->notes) }}</textarea>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('contracts.show', $contract) }}" class="btn btn-secondary">Hủy</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Lưu ý: hiện form sửa chưa cho sửa chi tiết dịch vụ; nếu cần có thể mở rộng tương tự create với filter Solution --}}
@endsection

