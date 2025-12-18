# Hệ thống Quản lý Hợp đồng CNTT (CLM)

Hệ thống quản lý hợp đồng phát triển dịch vụ CNTT được xây dựng bằng Laravel 11.

## 📋 Yêu cầu hệ thống

- PHP >= 8.2
- MySQL >= 5.7
- Composer
- Node.js & NPM
- Laragon (Windows)

## 🚀 Hướng dẫn Setup trên Laragon

### 1. Cài đặt Dependencies

```bash
# Cài đặt PHP packages
composer install

# Cài đặt NPM packages
npm install
```

### 2. Cấu hình môi trường

1. Copy file `.env.example` thành `.env`:
```bash
copy .env.example .env
```

3. Tạo database trong MySQL:
```sql
CREATE DATABASE quanlyhopdong CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

4. Generate application key:
```bash
php artisan key:generate
```

### 3. Chạy Migrations và Seeders

```bash
# Chạy migrations
php artisan migrate

# Chạy seeders để tạo dữ liệu mẫu
php artisan db:seed
```

### 4. Build Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 5. Chạy Queue và Scheduler (Windows)

**Queue Worker:**
```bash
php artisan queue:work
```

**Scheduler (tạo task trong Windows Task Scheduler):**
- Tạo task mới trong Task Scheduler
- Action: `php C:\laragon\www\QuanLyHopDong\artisan schedule:run`
- Trigger: Mỗi phút

Hoặc chạy thủ công:
```bash
php artisan schedule:work
```

### 6. Tạo Storage Link

```bash
php artisan storage:link
```

## 📦 Packages đã sử dụng

```bash
# Authentication
composer require laravel/breeze --dev

# Permission Management
composer require spatie/laravel-permission

# Excel Import/Export
composer require maatwebsite/excel
```

## 📁 Cấu trúc thư mục quan trọng

```
app/
├── Console/
│   ├── Commands/
│   │   ├── UpdateContractStatusCommand.php
│   │   └── CreateRemindersCommand.php
│   └── Kernel.php (scheduler config)
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php
│   │   ├── CustomerController.php
│   │   ├── ServiceController.php
│   │   ├── ContractController.php
│   │   ├── ContractImportController.php
│   │   ├── CalendarController.php
│   │   └── ReportController.php
│   ├── Requests/
│   │   ├── StoreCustomerRequest.php
│   │   ├── UpdateCustomerRequest.php
│   │   ├── StoreServiceRequest.php
│   │   ├── UpdateServiceRequest.php
│   │   ├── StoreContractRequest.php
│   │   ├── UpdateContractRequest.php
│   │   └── ImportContractRequest.php
│   └── Middleware/
├── Jobs/
│   ├── UpdateContractStatusJob.php
│   └── CreateRemindersJob.php
├── Models/
│   ├── User.php
│   ├── Customer.php
│   ├── Service.php
│   ├── Contract.php
│   ├── ContractItem.php
│   ├── Attachment.php
│   ├── Milestone.php
│   ├── Reminder.php
│   ├── Task.php
│   ├── StatusHistory.php
│   ├── Amendment.php
│   └── AuditLog.php
├── Notifications/
│   └── ContractExpiryReminder.php
└── Policies/
    ├── CustomerPolicy.php
    ├── ServicePolicy.php
    └── ContractPolicy.php

database/
├── migrations/
│   ├── create_customers_table.php
│   ├── create_services_table.php
│   ├── create_contracts_table.php
│   ├── create_contract_items_table.php
│   ├── create_attachments_table.php
│   ├── create_milestones_table.php
│   ├── create_reminders_table.php
│   ├── create_tasks_table.php
│   ├── create_status_history_table.php
│   ├── create_amendments_table.php
│   ├── create_audit_logs_table.php
│   └── add_sales_person_fields_to_users_table.php
└── seeders/
    ├── RoleSeeder.php
    ├── UserSeeder.php
    ├── CustomerSeeder.php
    ├── ServiceSeeder.php
    └── ContractSeeder.php

resources/
├── views/
│   ├── dashboard.blade.php
│   ├── customers/
│   ├── services/
│   ├── contracts/
│   ├── imports/
│   ├── calendar/
│   └── reports/
└── excel/
    └── contract_template.xlsx
```

## 👥 Tài khoản mẫu

Sau khi chạy seeders, các tài khoản sau sẽ được tạo:

| Email | Password | Role |
|-------|----------|------|
| admin@local | password | ADMIN |
| manager@local | password | MANAGER |
| sales1@local | password | SALES |

## 🔐 Roles và Permissions

### Roles
- **ADMIN**: Toàn quyền hệ thống, bao gồm quản lý người dùng (nhân viên kinh doanh, trưởng nhóm, các vai trò khác) với đầy đủ CRUD user và phân quyền/assign role
- **MANAGER**: Quản lý hợp đồng toàn đội
- **SALES**: Chỉ thấy hợp đồng mình phụ trách
- **LEGAL**: Xem hợp đồng
- **ACCOUNTING**: Xem hợp đồng
- **VIEWER**: Chỉ xem

### Permissions
- `contracts.create`
- `contracts.update`
- `contracts.delete`
- `contracts.view`
- `customers.*`
- `services.*`

## 📝 Chức năng chính

### 1. Dashboard
- Thống kê: Active / Near Expiry / Expired
- Danh sách hợp đồng sắp hết hạn trong 30 ngày

### 2. Quản lý Khách hàng
- CRUD đầy đủ
- Tìm kiếm theo tên, mã, email

### 3. Quản lý Dịch vụ
- CRUD đầy đủ
- Quản lý giá mặc định

### 4. Quản lý Hợp đồng
- CRUD đầy đủ
- Thêm nhiều dịch vụ (items) trong 1 hợp đồng
- Upload file đính kèm
- Filter theo KH, NVKD, trạng thái, ngày hết hạn
- Lịch sử trạng thái

### 5. Import Excel
- Upload file Excel
- Mapping cột tự động
- Xử lý trùng lặp: SKIP / UPDATE
- Tự động tạo Customer/Service nếu chưa có
- Tạo milestone EXPIRY sau import

### 6. Calendar
- Xem theo tháng
- Click ngày xem danh sách hợp đồng đến hạn
- API endpoint: `/calendar/events?month=...&year=...`

### 7. Báo cáo
- Báo cáo hợp đồng đến hạn trong tháng
- Xuất Excel

## 🔄 Scheduler & Jobs

### Commands
```bash
# Cập nhật trạng thái hợp đồng
php artisan contracts:update-status

# Tạo reminders
php artisan contracts:create-reminders
```

### Scheduled Tasks (app/Console/Kernel.php)
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('contracts:update-status')->daily();
    $schedule->command('contracts:create-reminders')->daily();
}
```

## 📊 Excel Template

File template: `resources/excel/contract_template.xlsx`

Các cột bắt buộc:
- `contract_no`: Số hợp đồng (unique)
- `customer_code`: Mã khách hàng
- `customer_name`: Tên khách hàng
- `sales_person_email`: Email NVKD
- `title`: Tiêu đề hợp đồng
- `start_date`: Ngày bắt đầu (YYYY-MM-DD)
- `end_date`: Ngày kết thúc (YYYY-MM-DD) - **BẮT BUỘC**
- `service_code`: Mã dịch vụ
- `service_name`: Tên dịch vụ
- `quantity`: Số lượng
- `unit_price`: Đơn giá
- `total_amount`: Tổng tiền

## 🧪 Checklist Test thủ công

### 1. Import Excel
- [ ] Upload file Excel đúng template
- [ ] Import thành công
- [ ] Tạo Customer/Service tự động nếu chưa có
- [ ] Tạo milestone EXPIRY
- [ ] Xử lý trùng contract_no (SKIP/UPDATE)

### 2. Near Expiry
- [ ] Hợp đồng còn <= 30 ngày tự động chuyển NEAR_EXPIRY
- [ ] Hiển thị trên Dashboard

### 3. Reminder
- [ ] Tạo reminders ở mốc 30/15/7/3/1 ngày
- [ ] Gửi notification trong app
- [ ] Gửi email (nếu cấu hình)

### 4. Calendar
- [ ] Hiển thị lịch tháng
- [ ] Click ngày xem danh sách hợp đồng
- [ ] API trả về JSON đúng format

### 5. Report
- [ ] Báo cáo hợp đồng đến hạn trong tháng
- [ ] Xuất Excel thành công

### 6. Solutions & Dịch vụ theo Giải pháp
- [ ] CRUD Giải pháp (Solution) hoạt động theo phân quyền (ADMIN/MANAGER tạo/sửa/xóa, các vai trò khác chỉ xem)
- [ ] Service luôn gắn với 1 Solution (hoặc UNCAT nếu chưa phân loại)
- [ ] Màn hình Dịch vụ: lọc theo Solution, xem dạng bảng thường và dạng gom nhóm theo Solution
- [ ] Form tạo/sửa Hợp đồng: lọc danh sách dịch vụ theo Solution khi chọn
- [ ] Import Excel: cột solution_code/solution_name mapping đúng vào Solution (nếu thiếu thì về UNCAT)
- [ ] Báo cáo hợp đồng đến hạn: lọc theo Solution và hiển thị cột Solution trong export

## 🔧 Cấu hình bổ sung

### Queue Connection (database)
Đã cấu hình sẵn trong `.env`:
```env
QUEUE_CONNECTION=database
```

### Mail Configuration (optional)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

## 📚 API Endpoints

### Calendar Events
```
GET /calendar/events?month=12&year=2024
```

Response:
```json
[
  {
    "title": "Hợp đồng ABC",
    "start": "2024-12-15",
    "url": "/contracts/1"
  }
]
```

### Export Report
```
GET /reports/expiry/export?month=12&year=2024
```

## 🐛 Troubleshooting

### Lỗi migration
```bash
php artisan migrate:fresh --seed
```

### Lỗi permission
```bash
php artisan permission:cache-reset
```

### Clear cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## 📄 License

Proprietary - Internal use only
