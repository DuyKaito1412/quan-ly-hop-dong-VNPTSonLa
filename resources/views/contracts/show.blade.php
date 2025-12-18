@extends('layouts.app')

@section('title', 'Chi tiết Hợp đồng')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> {{ $contract->contract_no }}</h5>
                <div>
                    <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-pencil"></i> Sửa
                    </a>
                    <a href="{{ route('contracts.index') }}" class="btn btn-sm btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">Số hợp đồng:</th>
                        <td><strong>{{ $contract->contract_no }}</strong></td>
                    </tr>
                    <tr>
                        <th>Khách hàng:</th>
                        <td>{{ $contract->customer->name }} ({{ $contract->customer->code }})</td>
                    </tr>
                    <tr>
                        <th>NVKD:</th>
                        <td>{{ $contract->salesPerson->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Tiêu đề:</th>
                        <td>{{ $contract->title }}</td>
                    </tr>
                    <tr>
                        <th>Ngày bắt đầu:</th>
                        <td>{{ $contract->start_date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Ngày kết thúc:</th>
                        <td>
                            <span class="{{ now()->diffInDays($contract->end_date, false) <= 30 ? 'text-danger fw-bold' : '' }}">
                                {{ $contract->end_date->format('d/m/Y') }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Trạng thái:</th>
                        <td>
                            @if($contract->status == 'ACTIVE')
                                <span class="badge bg-success">Active</span>
                            @elseif($contract->status == 'NEAR_EXPIRY')
                                <span class="badge bg-warning">Sắp hết hạn</span>
                            @elseif($contract->status == 'EXPIRED')
                                <span class="badge bg-danger">Hết hạn</span>
                            @else
                                <span class="badge bg-secondary">{{ $contract->status }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Tổng tiền:</th>
                        <td><strong>{{ number_format($contract->total_amount, 0, ',', '.') }} {{ $contract->currency }}</strong></td>
                    </tr>
                </table>
                
                @if($contract->description)
                <div class="mt-3">
                    <strong>Mô tả:</strong>
                    <p>{{ $contract->description }}</p>
                </div>
                @endif
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> Dịch vụ</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Giải pháp</th>
                            <th>Dịch vụ</th>
                            <th>Số lượng</th>
                            <th>Đơn giá</th>
                            <th>Thành tiền</th>
                            <th>Chu kỳ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contract->items as $item)
                            <tr>
                                <td>{{ $item->service->solution->name ?? 'Chưa phân loại' }}</td>
                                <td>{{ $item->service->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td><strong>{{ number_format($item->amount, 0, ',', '.') }}</strong></td>
                                <td>{{ $item->cycle ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Lịch sử trạng thái</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($contract->statusHistory->sortByDesc('changed_at') as $history)
                        <div class="list-group-item">
                            <small class="text-muted">{{ $history->changed_at->format('d/m/Y H:i') }}</small>
                            <div>
                                {{ $history->from_status ?? 'N/A' }} → 
                                <strong>{{ $history->to_status }}</strong>
                            </div>
                            @if($history->changer)
                                <small class="text-muted">bởi {{ $history->changer->name }}</small>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-paperclip"></i> File hợp đồng</h5>
            </div>
            <div class="card-body">
                @can('update', $contract)
                    <form action="{{ route('contracts.attachments.upload', $contract) }}" method="POST" enctype="multipart/form-data" class="mb-3">
                        @csrf
                        <div class="mb-2">
                            <label for="attachment-file" class="form-label">Tải file hợp đồng (PDF/DOC/DOCX)</label>
                            <input type="file" name="file" id="attachment-file" class="form-control" accept=".pdf,.doc,.docx" required>
                        </div>
                        <div class="mb-2">
                            <input type="text" name="description" class="form-control" placeholder="Mô tả (tuỳ chọn)">
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-upload"></i> Tải lên
                        </button>
                    </form>
                @endcan

                @if($contract->attachments->count())
                    <ul class="list-group list-group-flush">
                        @foreach($contract->attachments as $attachment)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $attachment->file_name }}</strong>
                                    @if($attachment->description)
                                        <div class="text-muted small">{{ $attachment->description }}</div>
                                    @endif
                                    <div class="text-muted small">
                                        {{ number_format(($attachment->file_size ?? 0) / 1024, 1) }} KB
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('contracts.attachments.view', [$contract, $attachment]) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Xem
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted mb-0">Chưa có file hợp đồng nào.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

