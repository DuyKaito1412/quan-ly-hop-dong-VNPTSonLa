# Hướng dẫn Setup và Code bổ sung

## 📝 Các file code đã được tạo

### ✅ Đã hoàn thành:
1. ✅ Migrations cho tất cả các bảng
2. ✅ Models với relationships đầy đủ
3. ✅ FormRequests với validation
4. ✅ Policies cho authorization
5. ✅ Controllers (Dashboard, Customer, Service, Contract, Import, Calendar, Report)
6. ✅ Jobs và Commands cho scheduler
7. ✅ Notifications
8. ✅ Seeders
9. ✅ Routes
10. ✅ Scheduler configuration

### ⏳ Cần bổ sung:
1. Views (Blade templates)
2. Excel template file
3. Một số middleware nếu cần

## 🎨 Tạo Views

### 1. Layout chính (resources/views/layouts/app.blade.php)

```blade
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quản lý Hợp đồng')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">CLM System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('contracts.index') }}">Hợp đồng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('customers.index') }}">Khách hàng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('services.index') }}">Dịch vụ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('calendar.index') }}">Lịch</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reports.expiry') }}">Báo cáo</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Đăng xuất</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
```

### 2. Dashboard (resources/views/dashboard.blade.php)

```blade
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Hợp đồng Active</h5>
                <h2>{{ $stats['active'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title">Sắp hết hạn</h5>
                <h2>{{ $stats['near_expiry'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <h5 class="card-title">Đã hết hạn</h5>
                <h2>{{ $stats['expired'] }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5>Hợp đồng sắp hết hạn trong 30 ngày</h5>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Số HĐ</th>
                    <th>Khách hàng</th>
                    <th>NVKD</th>
                    <th>Ngày hết hạn</th>
                    <th>Còn lại</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($nearExpiryContracts as $contract)
                    <tr>
                        <td>{{ $contract->contract_no }}</td>
                        <td>{{ $contract->customer->name }}</td>
                        <td>{{ $contract->salesPerson->name ?? 'N/A' }}</td>
                        <td>{{ $contract->end_date->format('d/m/Y') }}</td>
                        <td>{{ now()->diffInDays($contract->end_date) }} ngày</td>
                        <td>
                            <a href="{{ route('contracts.show', $contract) }}" class="btn btn-sm btn-primary">Xem</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Không có hợp đồng nào sắp hết hạn</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
```

### 3. Contracts Index (resources/views/contracts/index.blade.php)

Tạo view tương tự với form filter và bảng danh sách hợp đồng.

### 4. Contracts Create/Edit (resources/views/contracts/create.blade.php)

Tạo form với các trường cần thiết và JavaScript để thêm/xóa contract items động.

### 5. Import (resources/views/contracts/import.blade.php)

```blade
@extends('layouts.app')

@section('title', 'Import Hợp đồng')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Import Hợp đồng từ Excel</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('contracts.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-3">
                <label for="file" class="form-label">Chọn file Excel</label>
                <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls" required>
                <small class="form-text text-muted">
                    <a href="{{ route('contracts.import.template') }}">Tải template mẫu</a>
                </small>
            </div>
            
            <div class="mb-3">
                <label for="duplicate_action" class="form-label">Xử lý trùng lặp</label>
                <select class="form-select" id="duplicate_action" name="duplicate_action">
                    <option value="SKIP">Bỏ qua</option>
                    <option value="UPDATE">Cập nhật</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Import</button>
        </form>
        
        @if(session('errors'))
            <div class="alert alert-warning mt-3">
                <h6>Các lỗi:</h6>
                <ul>
                    @foreach(session('errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
@endsection
```

## 📊 Excel Template

Tạo file `resources/excel/contract_template.xlsx` với các cột:
- contract_no
- customer_code
- customer_name
- sales_person_email
- title
- start_date
- end_date
- service_code
- service_name
- quantity
- unit_price
- total_amount

## 🔧 Cấu hình bổ sung

### 1. Cập nhật .env

```env
CONTRACT_NEAR_EXPIRY_DAYS=30
```

### 2. Tạo storage link

```bash
php artisan storage:link
```

### 3. Chạy migrations và seeders

```bash
php artisan migrate
php artisan db:seed
```

### 4. Build assets

```bash
npm run dev
# hoặc
npm run build
```

## 🚀 Chạy Queue và Scheduler

### Queue Worker
```bash
php artisan queue:work
```

### Scheduler (Windows)
Tạo task trong Task Scheduler:
- Program: `php`
- Arguments: `C:\laragon\www\QuanLyHopDong\artisan schedule:run`
- Trigger: Mỗi phút

Hoặc chạy thủ công:
```bash
php artisan schedule:work
```

## ✅ Checklist hoàn thiện

- [ ] Tạo tất cả Views cần thiết
- [ ] Tạo Excel template file
- [ ] Test import Excel
- [ ] Test scheduler và queue
- [ ] Test phân quyền
- [ ] Test calendar
- [ ] Test báo cáo và export

## 📚 Tài liệu tham khảo

- Laravel 11 Documentation: https://laravel.com/docs/11.x
- Spatie Permission: https://spatie.be/docs/laravel-permission
- Maatwebsite Excel: https://docs.laravel-excel.com/

