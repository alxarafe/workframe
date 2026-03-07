@extends('partial.layout.main')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white border-bottom-0 pt-4 px-4">
        <h5 class="mb-0 text-dark fw-bold">
            <i class="fas fa-server text-primary me-2"></i>{{ $me->trans('database_status') }}
        </h5>
    </div>
    <div class="card-body p-4">
        <div class="alert alert-success shadow-sm mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ $me->trans('all_tables_up_to_date') }}
        </div>

        <div class="mt-4 p-5 border-0 rounded-4 text-center bg-light">
            <div class="mb-4">
                <i class="fas fa-database fa-3x text-warning opacity-50"></i>
            </div>
            <p class="text-muted small mb-4 mx-auto" style="max-width: 500px">
                {{ $me->trans('seed_warning_description') }}
            </p>
            <a href="index.php?module=WorkFrame&controller=Database&action=index&seed=true" class="btn btn-warning px-4 rounded-pill">
                <i class="fas fa-magic me-2"></i>{{ $me->trans('run_seed_data') }}
            </a>
        </div>
    </div>
</div>
@endsection
