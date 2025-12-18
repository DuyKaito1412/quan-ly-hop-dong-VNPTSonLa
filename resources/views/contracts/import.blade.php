@extends('layouts.app')

@section('title', 'Import Hợp đồng')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-upload"></i> Import Hợp đồng từ Excel</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <h6><i class="bi bi-info-circle"></i> Hướng dẫn:</h6>
            <ul class="mb-0">
                <li>Tải file template mẫu để biết định dạng</li>
                <li>File Excel phải có các cột: contract_no, customer_code, customer_name, sales_person_email, title, start_date, end_date, service_code, service_name, quantity, unit_price, total_amount</li>
                <li>end_date là bắt buộc, nếu thiếu sẽ bị bỏ qua</li>
                <li>Nếu contract_no trùng, có thể chọn SKIP (bỏ qua) hoặc UPDATE (cập nhật)</li>
            </ul>
        </div>
        
        <form action="{{ route('contracts.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-3">
                <label for="file" class="form-label">Chọn file Excel <span class="text-danger">*</span></label>
                <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept=".xlsx,.xls" required>
                @error('file')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">
                    <a href="{{ route('contracts.import.template') }}" class="text-decoration-none">
                        <i class="bi bi-download"></i> Tải template mẫu
                    </a>
                </small>
            </div>
            
            <div class="mb-3">
                <label for="duplicate_action" class="form-label">Xử lý trùng lặp</label>
                <select class="form-select" id="duplicate_action" name="duplicate_action">
                    <option value="SKIP">Bỏ qua (SKIP)</option>
                    <option value="UPDATE">Cập nhật (UPDATE)</option>
                </select>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('contracts.index') }}" class="btn btn-secondary">Hủy</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-upload"></i> Import
                </button>
            </div>
        </form>
        
        @if(session('errors'))
            <div class="alert alert-warning mt-3">
                <h6><i class="bi bi-exclamation-triangle"></i> Các lỗi:</h6>
                <ul class="mb-0">
                    @foreach(session('errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
@endsection

