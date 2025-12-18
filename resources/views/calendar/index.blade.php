@extends('layouts.app')

@section('title', 'Lịch Hợp đồng')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-calendar3"></i> Lịch Hợp đồng đến hạn</h5>
    </div>
    <div class="card-body">
        <div id="calendar"></div>
    </div>
</div>

@push('styles')
<style>
#calendar {
    max-width: 100%;
    margin: 0 auto;
}
.fc-event {
    cursor: pointer;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'vi',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,listWeek'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            fetch('{{ route("calendar.events") }}?month=' + (fetchInfo.start.getMonth() + 1) + '&year=' + fetchInfo.start.getFullYear())
                .then(response => response.json())
                .then(data => {
                    successCallback(data);
                })
                .catch(error => {
                    failureCallback(error);
                });
        },
        eventClick: function(info) {
            if (info.event.url) {
                window.location.href = info.event.url;
                return false;
            }
        }
    });
    calendar.render();
});
</script>
@endpush
@endsection

