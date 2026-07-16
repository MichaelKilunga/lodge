@extends('template.master')
@section('title', 'All Activity Logs')
@section('content')
    <div class="container-fluid fade-in">
        <div class="row align-items-center mb-4">
            <div class="col-md-6 col-sm-12">
                <h1 class="h3 text-gradient mb-1">All User Activity Logs</h1>
                <p class="text-muted mb-0 small">Complete archive of system events and user actions</p>
            </div>
            <div class="col-md-6 col-sm-12 text-md-end text-start mt-3 mt-md-0">
                <a href="{{ route('activity-log.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm fw-semibold">
                    <i class="fas fa-arrow-left me-2"></i> Back to Logs
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 50%;">Description</th>
                                <th style="width: 20%;">By User</th>
                                <th style="width: 20%;">Logged At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activities as $activity)
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-secondary border font-monospace">
                                            {{ $loop->iteration }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $activity->description }}</div>
                                    </td>
                                    <td>
                                        @if($activity->causer)
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user-circle text-muted me-2" style="font-size: 1.1rem;"></i>
                                                <span class="fw-semibold">{{ $activity->causer->name }}</span>
                                            </div>
                                        @else
                                            <span class="badge bg-secondary">System Agent</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-muted small font-monospace">
                                            <i class="far fa-clock me-1"></i>
                                            {{ $activity->created_at->format('M d, Y g:i A') }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="fas fa-history mb-3" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                        <p class="mb-0">No activities archived.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
