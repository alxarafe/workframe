@extends('partial.layout.main')

@section('header_actions')
    <form method="get" class="d-flex align-items-center gap-3">
        <input type="hidden" name="module" value="WorkFrame">
        <input type="hidden" name="controller" value="Report">
        <input type="hidden" name="action" value="customers">
        
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-light"><i class="fas fa-users text-muted"></i></span>
            <select name="customer_id" class="form-select" style="min-width: 200px;">
                <option value="">{{ $me->trans('all_customers') }}</option>
                @foreach($customers as $c)
                    @php $c = (array) $c; @endphp
                    <option value="{{ $c['id'] }}" {{ ($customer_id ?? '') == $c['id'] ? 'selected' : '' }}>{{ $c['name'] }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="input-group input-group-sm">
            <input type="date" name="date_from" class="form-control" value="{{ $date_from }}">
            <span class="input-group-text bg-light"><i class="fas fa-arrow-right text-muted small"></i></span>
            <input type="date" name="date_to" class="form-control" value="{{ $date_to }}">
        </div>
        
        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
            <i class="fas fa-filter me-1"></i>{{ $me->trans('filter') }}
        </button>
    </form>
@endsection

@section('content')
<div class="card shadow-sm border-0 rounded-4 overflow-hidden">
    <div class="card-header bg-white py-3 px-4">
        <h5 class="mb-0 fw-bold text-dark">
            <i class="fas fa-chart-line text-info me-2"></i>{{ $me->trans('customer_activity_report') }}
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light small text-uppercase text-muted">
                    <tr>
                        <th class="ps-4 fw-bold">{{ $me->trans('customer') }}</th>
                        <th class="fw-bold">{{ $me->trans('project_file') }}</th>
                        <th class="fw-bold">{{ $me->trans('work_order') }}</th>
                        <th class="text-center fw-bold pe-4">{{ $me->trans('parts_count') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($report_data as $row)
                    @php $row = (array) $row; @endphp
                    <tr>
                        <td class="ps-4 fw-bold text-dark">{{ $row['customer_name'] }}</td>
                        <td><span class="badge bg-light border border-secondary text-secondary rounded-3">{{ $row['project_file_name'] ?? '' }}</span></td>
                        <td class="small text-muted">{{ $row['work_order_name'] ?? '' }}</td>
                        <td class="text-center pe-4">
                            <span class="badge rounded-pill bg-info text-dark px-3 mt-1 fw-bold">{{ $row['part_count'] ?? 0 }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted small bg-light bg-opacity-10">
                            <i class="fas fa-folder-open fa-3x mb-3 d-block opacity-25"></i>
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
