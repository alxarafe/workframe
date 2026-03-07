@extends('partial.layout.main')

@section('header_actions')
    <a href="index.php?module=WorkFrame&controller=Mail&action=config" class="btn btn-sm btn-outline-info rounded-pill px-3 shadow-sm">
        <i class="fas fa-cog me-1"></i>{{ $me->trans('configure') }}
    </a>
@endsection

@section('content')

@if(empty($config['host']))
    <div class="alert alert-warning shadow-sm border-0 rounded-4 overflow-hidden mb-4 animate__animated animate__fadeIn">
        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>{{ $me->trans('mail_not_configured') }}
        <a href="index.php?module=WorkFrame&controller=Mail&action=config" class="alert-link ms-2">{{ $me->trans('configure_now') }}</a>
    </div>
@else
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden shadow-sm animate__animated animate__fadeIn">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-dark fw-bold">
                <i class="fas fa-envelope-open-text text-primary me-2"></i>{{ $me->trans('inbox') }}
            </h5>
            <span class="badge bg-light text-primary rounded-pill border border-primary px-3">{{ count($emails) }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light small text-uppercase text-muted">
                        <tr>
                            <th class="ps-4 fw-bold">{{ $me->trans('from') }}</th>
                            <th class="fw-bold">{{ $me->trans('subject') }}</th>
                            <th class="fw-bold">{{ $me->trans('date') }}</th>
                            <th class="text-end pe-4 fw-bold">{{ $me->trans('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($emails as $email)
                        <tr class="animate__animated animate__fadeInUp animate__faster">
                            <td class="ps-4">
                                <span class="text-primary small fw-bold">{{ $email['from'] }}</span>
                            </td>
                            <td>
                                <span class="d-block fw-bold text-dark">{{ $email['subject'] }}</span>
                            </td>
                            <td>
                                <span class="small text-muted"><i class="far fa-clock me-1"></i>{{ $email['date'] }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                                    <a href="index.php?module=WorkFrame&controller=Mail&action=delete&mail_id={{ $email['id'] }}" 
                                       class="btn btn-sm btn-white text-danger border-start-0" 
                                       onclick="return confirm('{{ $me->trans('confirm_delete_email') }}')"
                                       title="{{ $me->trans('delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted small bg-light bg-opacity-10">
                                <i class="fas fa-inbox fa-3x mb-3 d-block opacity-25"></i>
                                <span class="fs-6 d-block">{{ $me->trans('no_emails_found') }}</span>
                                <p class="mb-0 mt-2 opacity-50">{{ $me->trans('inbox_empty_explanation') }}</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

@endsection
