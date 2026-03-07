@extends('partial.layout.main')

@section('header_actions')
    <form method="get" class="d-flex align-items-center gap-3">
        <input type="hidden" name="module" value="WorkFrame">
        <input type="hidden" name="controller" value="Report">
        <input type="hidden" name="action" value="workers">
        
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-light"><i class="fas fa-hard-hat text-muted"></i></span>
            <select name="worker_id" class="form-select" style="min-width: 180px;">
                <option value="">{{ $me->trans('all_workers') }}</option>
                @foreach($workers as $w)
                    @php $w = (array) $w; @endphp
                    <option value="{{ $w['id'] }}" {{ ($worker_id ?? '') == $w['id'] ? 'selected' : '' }}>{{ $w['name'] }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="input-group input-group-sm">
            <input type="date" name="date_from" class="form-control" value="{{ $date_from }}">
            <span class="input-group-text bg-light"><i class="fas fa-arrow-right text-muted small"></i></span>
            <input type="date" name="date_to" class="form-control" value="{{ $date_to }}">
        </div>
        
        <div class="btn-group btn-group-sm shadow-sm">
            <button type="submit" class="btn btn-primary px-3">
                <i class="fas fa-filter me-1"></i>{{ $me->trans('filter') }}
            </button>
            <a href="index.php?module=WorkFrame&controller=Report&action=workers&format=pdf&date_from={{ $date_from }}&date_to={{ $date_to }}&worker_id={{ $worker_id ?? '' }}" class="btn btn-danger px-3">
                <i class="fas fa-file-pdf me-1"></i>{{ $me->trans('pdf') }}
            </a>
        </div>
    </form>
@endsection

@section('content')
<div class="card shadow-sm border-0 rounded-4 overflow-hidden">
    <div class="card-header bg-white py-3 px-4">
        <h5 class="mb-0 fw-bold text-dark">
            <i class="fas fa-calendar-check text-primary me-2"></i>{{ $me->trans('worker_attendance_report') }}
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light small text-uppercase text-muted">
                    <tr>
                        <th class="ps-4 fw-bold">{{ $me->trans('worker') }}</th>
                        <th class="fw-bold">{{ $me->trans('date') }}</th>
                        <th class="fw-bold" style="min-width: 150px;">{{ $me->trans('order') }}</th>
                        <th class="fw-bold">{{ $me->trans('morning') }}</th>
                        <th class="fw-bold">{{ $me->trans('afternoon') }}</th>
                        <th class="fw-bold">{{ $me->trans('travel') }}</th>
                        <th class="text-end pe-4 fw-bold">{{ $me->trans('allowances') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($report_data as $row)
                    @php $row = (array) $row; @endphp
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-dark d-block">{{ $row['worker_name'] }}</span>
                        </td>
                        <td>
                            <span class="small text-muted">{{ $row['date'] }}</span>
                        </td>
                        <td>
                            <span class="text-truncate d-inline-block small fw-bold text-secondary" style="max-width: 200px;" title="{{ $row['work_order_name'] }}">
                                {{ $row['work_order_name'] }}
                            </span>
                        </td>
                        <td>
                            @if(!empty($row['morning_from']))
                                <span class="badge bg-light border border-secondary text-dark fw-normal">{{ substr($row['morning_from'], 0, 5) }} - {{ substr($row['morning_to'], 0, 5) }}</span>
                            @endif
                        </td>
                        <td>
                            @if(!empty($row['afternoon_from']))
                                <span class="badge bg-light border border-secondary text-dark fw-normal">{{ substr($row['afternoon_from'], 0, 5) }} - {{ substr($row['afternoon_to'], 0, 5) }}</span>
                            @endif
                        </td>
                        <td>
                            @if(!empty($row['going_start']))
                                <div class="small text-muted d-flex flex-column gap-1">
                                    <span><i class="fas fa-sign-out-alt me-1 text-primary small"></i>{{ substr($row['going_start'], 0, 5) }}-{{ substr($row['going_end'], 0, 5) }}</span>
                                    <span><i class="fas fa-sign-in-alt me-1 text-success small"></i>{{ substr($row['back_start'], 0, 5) }}-{{ substr($row['back_end'], 0, 5) }}</span>
                                </div>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            @if(!empty($row['allowances']))
                                <span class="fw-bold text-success">{{ number_format((float)$row['allowances'], 2) }}€</span>
                            @else
                                <span class="text-muted opacity-25">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted small bg-light bg-opacity-10">
                            <i class="fas fa-clipboard-list fa-3x mb-3 d-block opacity-25"></i>
                            <span class="fs-6 d-block">{{ $me->trans('no_data_found_for_filter') }}</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
