@extends('partial.layout.main')

@section('header_actions')
    <form method="get" class="d-flex align-items-center gap-2">
        <input type="hidden" name="module" value="WorkFrame">
        <input type="hidden" name="controller" value="Search">
        <input type="hidden" name="action" value="index">
        <div class="input-group input-group-sm">
            <input type="text" name="q" class="form-control" placeholder="{{ $me->trans('search_placeholder') }}" value="{{ $query }}">
            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
        </div>
    </form>
@endsection

@section('content')

@if(empty($results) && !empty($query))
    <div class="alert alert-info shadow-sm mb-4">
        <i class="fas fa-info-circle me-2"></i>{{ $me->trans('no_results_found', ['query' => $query]) }}
    </div>
@endif

@if(!empty($results))
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0 text-primary">
            <i class="fas fa-search me-2"></i>{{ $me->trans('search_results') }}
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light small text-uppercase text-muted">
                    <tr>
                        <th class="ps-4">{{ $me->trans('type') }}</th>
                        <th>{{ $me->trans('id') }}</th>
                        <th>{{ $me->trans('name') }}</th>
                        <th class="text-end pe-4">{{ $me->trans('actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $item)
                    @php $item = (array) $item; @endphp
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-light border border-info text-info rounded-pill px-3">
                                {{ ucfirst(str_replace('_', ' ', $item['type'])) }}
                            </span>
                        </td>
                        <td class="font-monospace text-muted small">#{{ $item['id'] }}</td>
                        <td class="fw-bold text-dark">{{ $item['name'] }}</td>
                        <td class="text-end pe-4">
                            @php
                                $controllerMap = [
                                    'customer' => 'Customer',
                                    'project_file' => 'ProjectFile',
                                    'work_order' => 'WorkOrder',
                                    'worker' => 'Worker',
                                ];
                                $ctrl = $controllerMap[$item['type']] ?? 'Index';
                            @endphp
                            <a href="index.php?module=WorkFrame&controller={{ $ctrl }}&id={{ $item['id'] }}" class="btn btn-sm btn-outline-info rounded-pill px-3">
                                <i class="fas fa-eye me-1"></i>{{ $me->trans('view') }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@endsection
