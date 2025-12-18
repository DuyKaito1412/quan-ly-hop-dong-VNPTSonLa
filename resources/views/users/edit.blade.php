@extends('layouts.app')

@section('title', 'Sửa Người dùng')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Sửa Người dùng</h5>
                <a href="{{ route('users.index') }}" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Tên</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mật khẩu mới (bỏ trống nếu không đổi)</label>
                        <input type="password" name="password" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nhập lại mật khẩu mới</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Mã nhân viên</label>
                            <input type="text" name="employee_code" class="form-control" value="{{ old('employee_code', $user->employee_code) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Điện thoại</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Là nhân viên kinh doanh?</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="is_sales_person" value="1" {{ old('is_sales_person', $user->is_sales_person) ? 'checked' : '' }}>
                                <label class="form-check-label">Sales</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $user->address) }}">
                    </div>

                    @if(auth()->id() !== $user->id)
                        <div class="mb-3">
                            <label class="form-label">Roles</label>
                            <div class="row">
                                @foreach($roles as $role)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                name="roles[]"
                                                value="{{ $role->name }}"
                                                id="role-{{ $role->id }}"
                                                {{ in_array($role->name, old('roles', $userRoles)) ? 'checked' : '' }}
                                            >
                                            <label class="form-check-label" for="role-{{ $role->id }}">
                                                {{ $role->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            Bạn đang chỉnh sửa tài khoản của chính mình. Việc thay đổi roles nên thực hiện cẩn trọng và có thể giới hạn qua cấu hình hệ thống.
                        </div>
                    @endif

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Lưu
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


