@extends('partial.layout.main')

@section('content')
<div class="row">
    <div class="col-md-8 col-lg-6 mx-auto">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-primary text-white py-3 px-4">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-cog me-2"></i>{{ $me->trans('mail_configuration') }}
                </h5>
            </div>
            <div class="card-body p-4 p-md-5">
                <form method="post" action="index.php?module=WorkFrame&controller=Mail&action=config">
                    <div class="mb-4">
                        <label for="host" class="form-label small fw-bold text-muted">{{ $me->trans('imap_host') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-server text-muted"></i></span>
                            <input type="text" name="host" id="host" class="form-control border-start-0" value="{{ $config['host'] ?? '' }}" placeholder="imap.gmail.com">
                        </div>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-md-8">
                            <div class="mb-4">
                                <label for="username" class="form-label small fw-bold text-muted">{{ $me->trans('username') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                                    <input type="text" name="username" id="username" class="form-control border-start-0" value="{{ $config['username'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label for="port" class="form-label small fw-bold text-muted">{{ $me->trans('port') }}</label>
                                <input type="number" name="port" id="port" class="form-control" value="{{ $config['port'] ?? 993 }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label small fw-bold text-muted">{{ $me->trans('password') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                            <input type="password" name="password" id="password" class="form-control border-start-0" value="{{ $config['password'] ?? '' }}">
                        </div>
                    </div>

                    <div class="mb-5">
                        <div class="form-check form-switch p-0 d-flex align-items-center justify-content-between bg-light rounded-3 p-3">
                            <label for="ssl" class="form-check-label fw-bold text-muted mb-0 ps-2">{{ $me->trans('use_ssl') }}</label>
                            <input type="checkbox" name="ssl" id="ssl" class="form-check-input float-none ms-3" style="width: 3.5rem; height: 1.75rem;" {{ ($config['ssl'] ?? true) ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="d-grid gap-3">
                        <button type="submit" class="btn btn-primary py-2 rounded-pill shadow-sm">
                            <i class="fas fa-save me-2"></i>{{ $me->trans('save_configuration') }}
                        </button>
                        <a href="index.php?module=WorkFrame&controller=Mail" class="btn btn-link text-muted btn-sm text-decoration-none">
                            {{ $me->trans('cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
