@extends('layouts.app')

@section('title', 'Sửa Dịch vụ')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-pencil"></i> Sửa Dịch vụ: {{ $service->name }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('services.update', $service) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="code" class="form-label">Mã dịch vụ <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $service->code) }}" required>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="solution_id" class="form-label">Giải pháp <span class="text-danger">*</span></label>
                    <select class="form-select @error('solution_id') is-invalid @enderror" id="solution_id" name="solution_id" required>
                        <option value="">Chọn giải pháp</option>
                        @foreach($solutions as $solution)
                            <option value="{{ $solution->id }}" {{ old('solution_id', $service->solution_id) == $solution->id ? 'selected' : '' }}>
                                {{ $solution->name }} ({{ $solution->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('solution_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Tên dịch vụ <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $service->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="unit" class="form-label">Đơn vị tính</label>
                    <input type="text" class="form-control @error('unit') is-invalid @enderror" id="unit" name="unit" value="{{ old('unit', $service->unit) }}">
                    @error('unit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="default_price" class="form-label">Giá mặc định (VNĐ)</label>
                    <input type="number" class="form-control @error('default_price') is-invalid @enderror" id="default_price" name="default_price" value="{{ old('default_price', $service->default_price) }}" min="0" step="1000">
                    @error('default_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $service->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="is_active" class="form-label">Trạng thái</label>
                <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active">
                    <option value="1" {{ old('is_active', $service->is_active) == 1 ? 'selected' : '' }}>Hoạt động</option>
                    <option value="0" {{ old('is_active', $service->is_active) == 0 ? 'selected' : '' }}>Không hoạt động</option>
                </select>
                @error('is_active')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('services.index') }}" class="btn btn-secondary">Hủy</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

