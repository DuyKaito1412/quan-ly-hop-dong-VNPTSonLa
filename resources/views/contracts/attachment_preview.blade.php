@extends('layouts.app')

@section('title', 'Xem file hợp đồng')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-file-earmark-word"></i>
                    Xem trước file: {{ $attachment->file_name }}
                </h5>
                <a href="{{ route('contracts.show', $contract) }}" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại hợp đồng
                </a>
            </div>
            <div class="card-body p-0" style="height: 80vh;">
                <iframe 
                    src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode($fileUrl) }}" 
                    width="100%" 
                    height="100%" 
                    frameborder="0">
                </iframe>
            </div>
        </div>
    </div>
</div>
@endsection


