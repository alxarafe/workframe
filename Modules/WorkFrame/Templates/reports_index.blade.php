@extends('partial.layout.main')

@section('content')
<div class="row g-4">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h5 class="mb-0">{{ $me->trans('worker_report') }}</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3 pt-3">
                    <i class="fas fa-hard-hat fa-4x text-primary opacity-50"></i>
                </div>
                <p class="text-muted small text-center mb-4">{{ $me->trans('worker_report_description') }}</p>
                <div class="d-grid">
                    <a href="index.php?module=WorkFrame&controller=Report&action=workers" class="btn btn-primary">
                        <i class="fas fa-file-invoice me-2"></i>{{ $me->trans('generate') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header border-bottom-0 bg-transparent">
                <h5 class="mb-0">{{ $me->trans('customer_report') }}</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3 pt-3">
                    <i class="fas fa-users fa-4x text-info opacity-50"></i>
                </div>
                <p class="text-muted small text-center mb-4">{{ $me->trans('customer_report_description') }}</p>
                <div class="d-grid">
                    <a href="index.php?module=WorkFrame&controller=Report&action=customers" class="btn btn-info">
                        <i class="fas fa-file-invoice me-2"></i>{{ $me->trans('generate') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
