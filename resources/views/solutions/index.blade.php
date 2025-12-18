@extends('layouts.app')

@section('title', 'Quản lý Giải pháp')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-layers"></i> Danh sách Giải pháp</h5>
        @can('create', \App\Models\Solution::class)
            <a href="{{ route('solutions.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Thêm mới
            </a>
        @endcan
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('solutions.index') }}" class="mb-3">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên, mã..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="is_active" class="form-select">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Không hoạt động</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search"></i> Lọc
                    </button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Tên Giải pháp</th>
                        <th>Mô tả</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($solutions as $solution)
                        <tr>
                            <td><strong>{{ $solution->code }}</strong></td>
                            <td>{{ $solution->name }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($solution->description, 60) }}</td>
                            <td>
                                @if($solution->is_active)
                                    <span class="badge bg-success">Hoạt động</span>
                                @else
                                    <span class="badge bg-secondary">Không hoạt động</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('solutions.show', $solution) }}" class="btn btn-outline-info" title="Xem">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('update', $solution)
                                        <a href="{{ route('solutions.edit', $solution) }}" class="btn btn-outline-primary" title="Sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endcan
                                    @can('delete', $solution)
                                        <form action="{{ route('solutions.destroy', $solution) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa giải pháp này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0">Không có giải pháp nào</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $solutions->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection


