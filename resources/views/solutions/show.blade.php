@extends('layouts.app')

@section('title', 'Chi tiết Giải pháp')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-layers"></i> Chi tiết Giải pháp</h5>
        <div>
            @can('update', $solution)
                <a href="{{ route('solutions.edit', $solution) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-pencil"></i> Sửa
                </a>
            @endcan
            <a href="{{ route('solutions.index') }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Mã Giải pháp</dt>
            <dd class="col-sm-9">{{ $solution->code }}</dd>

            <dt class="col-sm-3">Tên Giải pháp</dt>
            <dd class="col-sm-9">{{ $solution->name }}</dd>

            <dt class="col-sm-3">Mô tả</dt>
            <dd class="col-sm-9">{{ $solution->description ?? '-' }}</dd>

            <dt class="col-sm-3">Trạng thái</dt>
            <dd class="col-sm-9">
                @if($solution->is_active)
                    <span class="badge bg-success">Hoạt động</span>
                @else
                    <span class="badge bg-secondary">Không hoạt động</span>
                @endif
            </dd>

            <dt class="col-sm-3">Ngày tạo</dt>
            <dd class="col-sm-9">{{ $solution->created_at?->format('d/m/Y H:i') }}</dd>

            <dt class="col-sm-3">Ngày cập nhật</dt>
            <dd class="col-sm-9">{{ $solution->updated_at?->format('d/m/Y H:i') }}</dd>
        </dl>
    </div>
</div>
@endsection


