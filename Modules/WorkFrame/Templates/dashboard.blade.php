@extends('partial.layout.main')

@section('header_actions')
    <form method="get" class="d-flex align-items-center gap-2">
        <input type="hidden" name="module" value="WorkFrame">
        <input type="hidden" name="controller" value="Dashboard">
        <input type="hidden" name="action" value="index">
        <label for="date" class="form-label mb-0 small text-muted">{{ $me->trans('show_from') }}:</label>
        <input type="date" name="date" id="date" class="form-control form-control-sm" value="{{ $filter_date }}">
        <button type="submit" class="btn btn-sm btn-primary">
            <i class="fas fa-filter"></i>
        </button>
    </form>
@endsection

@section('content')

<div class="row g-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header pb-0">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab-orders">
                            <i class="fas fa-calendar-alt me-2"></i>{{ $me->trans('work_orders') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-vehicles">
                            <i class="fas fa-truck me-2"></i>{{ $me->trans('vehicles') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-workers">
                            <i class="fas fa-users me-2"></i>{{ $me->trans('workers') }}
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-orders">
                        <div id="calendar-orders" style="min-height:500px"></div>
                    </div>
                    <div class="tab-pane fade" id="tab-vehicles">
                        <div id="calendar-vehicles" style="min-height:500px"></div>
                    </div>
                    <div class="tab-pane fade" id="tab-workers">
                        <div id="calendar-workers" style="min-height:500px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">{{ $me->trans('active_work_orders') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>{{ $me->trans('file') }}</th>
                                <th>{{ $me->trans('name') }}</th>
                                <th>{{ $me->trans('customer') }}</th>
                                <th>{{ $me->trans('start') }}</th>
                                <th>{{ $me->trans('end') }}</th>
                                <th>{{ $me->trans('hour') }}</th>
                                <th>{{ $me->trans('foreman') }}</th>
                                <th>{{ $me->trans('location') }}</th>
                                <th class="text-center">{{ $me->trans('resources') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fast_view as $order)
                            <tr>
                                <td>
                                    <a href="index.php?module=WorkFrame&controller=WorkOrder&id={{ $order['id'] }}" class="fw-bold">
                                        #{{ $order['id'] }}
                                    </a>
                                </td>
                                <td><span class="badge bg-secondary">{{ $order['project_file_name'] }}</span></td>
                                <td>{{ $order['name'] }}</td>
                                <td>{{ $order['customer_name'] }}</td>
                                <td>{{ $order['date'] }}</td>
                                <td>{{ $order['end_date'] }}</td>
                                <td><i class="far fa-clock me-1 text-muted"></i>{{ $order['start_time'] }}</td>
                                <td>{{ $order['foreman_name'] }}</td>
                                <td>
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $order['locality'] }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-info text-dark" title="{{ $order['workers'] }}">
                                        <i class="fas fa-user me-1"></i>{{ count(explode(',', $order['workers'])) }}
                                    </span>
                                    <span class="badge rounded-pill bg-warning text-dark" title="{{ $order['vehicles'] }}">
                                        <i class="fas fa-truck me-1"></i>{{ count(explode(',', $order['vehicles'])) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    function initCalendar(el, events) {
        if (!el || el.innerHTML !== '') return;
        new FullCalendar.Calendar(el, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            themeSystem: 'standard',
            events: events,
            eventClick: function(info) {
                if (info.event.url) { 
                    window.location.href = info.event.url; 
                    info.jsEvent.preventDefault(); 
                }
            }
        }).render();
    }
    
    initCalendar(document.getElementById('calendar-orders'), {!! $calendar_work_orders !!});

    // Lazy init other calendars on tab show
    document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(tabEl => {
        tabEl.addEventListener('shown.bs.tab', function(e) {
            const target = e.target.getAttribute('href');
            if (target === '#tab-vehicles') {
                initCalendar(document.getElementById('calendar-vehicles'), {!! $calendar_vehicles !!});
            } else if (target === '#tab-workers') {
                initCalendar(document.getElementById('calendar-workers'), {!! $calendar_workers !!});
            }
        });
    });
});
</script>
@endpush
@endsection
