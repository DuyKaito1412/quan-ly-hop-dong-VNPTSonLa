@extends('layouts.app')

@section('title', 'Thêm Hợp đồng')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-file-earmark-plus"></i> Thêm Hợp đồng mới</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('contracts.store') }}" method="POST" id="contractForm">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="contract_no" class="form-label">Số hợp đồng <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('contract_no') is-invalid @enderror" id="contract_no" name="contract_no" value="{{ old('contract_no') }}" required>
                    @error('contract_no')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="customer_id" class="form-label">Khách hàng <span class="text-danger">*</span></label>
                    <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                        <option value="">Chọn khách hàng</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
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
                    <select class="form-select @error('sales_person_id') is-invalid @enderror" id="sales_person_id" name="sales_person_id">
                        <option value="">Chọn NVKD</option>
                        @foreach($salesPeople as $person)
                            <option value="{{ $person->id }}" {{ old('sales_person_id') == $person->id ? 'selected' : '' }}>
                                {{ $person->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('sales_person_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                        <option value="DRAFT" {{ old('status', 'DRAFT') == 'DRAFT' ? 'selected' : '' }}>Draft</option>
                        <option value="ACTIVE" {{ old('status') == 'ACTIVE' ? 'selected' : '' }}>Active</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="start_date" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                    @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="end_date" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                    @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <hr>
            <h5 class="mb-3"><i class="bi bi-list-ul"></i> Dịch vụ</h5>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Lọc theo Giải pháp</label>
                    <select id="solution_filter" class="form-select">
                        <option value="">-- Tất cả giải pháp --</option>
                        @foreach($solutions as $solution)
                            <option value="{{ $solution->id }}">{{ $solution->name }} ({{ $solution->code }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="items-container">
                <div class="item-row mb-3 p-3 border rounded">
                    <div class="row">
                        <div class="col-md-5 mb-2">
                            <label class="form-label">Dịch vụ <span class="text-danger">*</span></label>
                            <select class="form-select service-select" name="items[0][service_id]" required>
                                <option value="">Chọn dịch vụ</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}"
                                            data-price="{{ $service->default_price ?? 0 }}"
                                            data-solution-id="{{ $service->solution_id }}">
                                        {{ $service->name }} ({{ $service->code }}) - {{ $service->solution->name ?? 'Chưa phân loại' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Số lượng <span class="text-danger">*</span></label>
                            <input type="number" class="form-control quantity-input" name="items[0][quantity]" value="1" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Đơn giá <span class="text-danger">*</span></label>
                            <input type="number" class="form-control price-input" name="items[0][unit_price]" min="0" step="1000" required>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Thành tiền</label>
                            <input type="text" class="form-control amount-display" readonly>
                        </div>
                        <div class="col-md-1 mb-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm remove-item" style="display: none;">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Chu kỳ</label>
                            <select class="form-select" name="items[0][cycle]">
                                <option value="">--</option>
                                <option value="MONTHLY">Hàng tháng</option>
                                <option value="QUARTERLY">Hàng quý</option>
                                <option value="YEARLY">Hàng năm</option>
                                <option value="ONE_TIME">Một lần</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="add-item">
                <i class="bi bi-plus-circle"></i> Thêm dịch vụ
            </button>
            
            <div class="mb-3">
                <label for="total_amount" class="form-label">Tổng tiền</label>
                <input type="number" class="form-control" id="total_amount" name="total_amount" readonly>
            </div>
            
            <div class="mb-3">
                <label for="currency" class="form-label">Loại tiền</label>
                <select class="form-select" id="currency" name="currency">
                    <option value="VND" {{ old('currency', 'VND') == 'VND' ? 'selected' : '' }}>VND</option>
                    <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="terms" class="form-label">Điều khoản</label>
                <textarea class="form-control" id="terms" name="terms" rows="4">{{ old('terms') }}</textarea>
            </div>
            
            <div class="mb-3">
                <label for="notes" class="form-label">Ghi chú</label>
                <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('contracts.index') }}" class="btn btn-secondary">Hủy</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Lưu
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let itemIndex = 1;

document.getElementById('add-item').addEventListener('click', function() {
    const container = document.getElementById('items-container');
    const newItem = container.firstElementChild.cloneNode(true);
    
    // Update indices
    newItem.querySelectorAll('select, input').forEach(el => {
        if (el.name) {
            el.name = el.name.replace(/\[0\]/, `[${itemIndex}]`);
            el.value = '';
        }
    });
    
    newItem.querySelector('.remove-item').style.display = 'block';
    newItem.querySelector('.amount-display').value = '';
    
    container.appendChild(newItem);
    itemIndex++;
    
    attachItemEvents(newItem);
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-item')) {
        const itemRow = e.target.closest('.item-row');
        if (document.querySelectorAll('.item-row').length > 1) {
            itemRow.remove();
            calculateTotal();
        }
    }
});

function attachItemEvents(itemRow) {
    const serviceSelect = itemRow.querySelector('.service-select');
    const quantityInput = itemRow.querySelector('.quantity-input');
    const priceInput = itemRow.querySelector('.price-input');
    const amountDisplay = itemRow.querySelector('.amount-display');
    
    serviceSelect.addEventListener('change', function() {
        const price = this.options[this.selectedIndex].dataset.price;
        if (price) {
            priceInput.value = price;
            calculateAmount(itemRow);
        }
    });
    
    quantityInput.addEventListener('input', () => calculateAmount(itemRow));
    priceInput.addEventListener('input', () => calculateAmount(itemRow));
}

function calculateAmount(itemRow) {
    const quantity = parseFloat(itemRow.querySelector('.quantity-input').value) || 0;
    const price = parseFloat(itemRow.querySelector('.price-input').value) || 0;
    const amount = quantity * price;
    itemRow.querySelector('.amount-display').value = amount.toLocaleString('vi-VN');
    calculateTotal();
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        total += quantity * price;
    });
    document.getElementById('total_amount').value = total;
}

// Attach events to first item
attachItemEvents(document.querySelector('.item-row'));

// Filter services by solution
document.getElementById('solution_filter').addEventListener('change', function() {
    const solutionId = this.value;
    document.querySelectorAll('.service-select').forEach(select => {
        Array.from(select.options).forEach(option => {
            if (!option.value) return;
            const optSolutionId = option.dataset.solutionId || '';
            option.hidden = solutionId && optSolutionId !== solutionId;
        });
        // Nếu option đang chọn bị ẩn thì reset
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption && selectedOption.hidden) {
            select.value = '';
        }
    });
});
</script>
@endpush
@endsection

