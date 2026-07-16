@extends('template.master')
@section('title', 'SMS Gateway Monitoring')
@section('content')
    <div id="sms-monitoring-page" class="fade-in">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 text-gradient mb-1">SMS Gateway Monitor</h1>
                        <p class="text-muted mb-0">Track live API balance, usage history, and transmission performance</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Live Balance & Stats Panel -->
        <div class="row mb-4">
            <!-- Gateway Balance Card -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card card-lh h-100 shadow-sm border-0 bg-gradient-dark text-white" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                    <div class="card-body d-flex flex-column justify-content-between p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="text-muted-light small text-uppercase fw-bold tracking-wider">SMS Provider Balance</span>
                                <h2 class="display-6 fw-bold mt-1 mb-0 text-warning">
                                    @if(isset($balanceData['balance']))
                                        @if(is_numeric($balanceData['balance']))
                                            {{ number_format($balanceData['balance'], 0) }} <span class="fs-5 text-muted-light">Credits</span>
                                        @else
                                            <span class="fs-5">{{ $balanceData['balance'] }}</span>
                                        @endif
                                    @else
                                        <span class="fs-4">Unavailable</span>
                                    @endif
                                </h2>
                            </div>
                            <div class="icon-shape bg-warning bg-opacity-20 text-warning rounded-3 p-3">
                                <i class="fas fa-wallet fa-lg"></i>
                            </div>
                        </div>
                        <div class="mt-4 border-top border-secondary pt-3">
                            <div class="row text-center">
                                <div class="col-6 border-end border-secondary">
                                    <div class="text-muted-light small">Gateway Status</div>
                                    <span class="badge bg-success rounded-pill px-2.5 py-1 mt-1">
                                        <i class="fas fa-check-circle me-1"></i> Active
                                    </span>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted-light small">API Endpoint</div>
                                    <span class="text-white-50 small d-block mt-1">SkyPush</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total SMS Sent -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card card-lh h-100 shadow-sm border-0">
                    <div class="card-body d-flex flex-column justify-content-between p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="text-muted small text-uppercase fw-bold tracking-wider">Total Sent Messages</span>
                                <h2 class="display-6 fw-bold mt-1 mb-0 text-primary">{{ number_format($totalSent) }}</h2>
                            </div>
                            <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                                <i class="fas fa-paper-plane fa-lg"></i>
                            </div>
                        </div>
                        <div class="mt-4 border-top pt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Successful Delivery</span>
                                <span class="fw-bold text-success">
                                    @if($totalSent > 0)
                                        {{ round(($totalSuccess / $totalSent) * 100, 1) }}%
                                    @else
                                        100%
                                    @endif
                                </span>
                            </div>
                            <div class="progress progress-lh mt-2" style="height: 6px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $totalSent > 0 ? ($totalSuccess / $totalSent) * 100 : 0 }}%" 
                                     aria-valuenow="{{ $totalSent > 0 ? ($totalSuccess / $totalSent) * 100 : 100 }}" 
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SMS Delivery Breakdown -->
            <div class="col-xl-4 col-md-12 mb-4">
                <div class="card card-lh h-100 shadow-sm border-0">
                    <div class="card-body d-flex flex-column justify-content-between p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="text-muted small text-uppercase fw-bold tracking-wider">Transmission Status</span>
                                <h2 class="display-6 fw-bold mt-1 mb-0 text-success">{{ number_format($totalSuccess) }} <span class="fs-5 text-muted fw-normal">OK</span></h2>
                            </div>
                            <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-3">
                                <i class="fas fa-check-double fa-lg"></i>
                            </div>
                        </div>
                        <div class="mt-4 border-top pt-3">
                            <div class="row text-center">
                                <div class="col-6 border-end">
                                    <div class="text-muted small">Success Logged</div>
                                    <span class="fw-bold text-success">{{ number_format($totalSuccess) }}</span>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">Failures Logged</div>
                                    <span class="fw-bold text-danger">{{ number_format($totalFailed) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Log Table -->
        <div class="row">
            <div class="col-12">
                <div class="card card-lh shadow-sm border-0">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-history text-muted me-2"></i>
                            SMS Outbox & Transmission Logs
                        </h5>
                        <small class="text-muted">Review real-time transmission reports and server responses</small>
                    </div>
                    <div class="card-body px-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 15%;">Recipient</th>
                                        <th style="width: 45%;">Message Content</th>
                                        <th style="width: 10%;" class="text-center">Pages / Chars</th>
                                        <th style="width: 15%;" class="text-center">Gateway Response</th>
                                        <th style="width: 10%;" class="text-center">Status</th>
                                        <th style="width: 15%;" class="text-end">Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($logs as $log)
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark">
                                                    <i class="fas fa-phone-alt text-muted small me-1"></i>
                                                    {{ $log->recipient }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-muted text-wrap" style="max-height: 80px; overflow-y: auto; font-size: 0.9rem;">
                                                    {{ $log->message }}
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border font-monospace">
                                                    {{ $log->page_count }} {{ Str::plural('page', $log->page_count) }}
                                                </span>
                                                <div class="text-muted small mt-1 font-monospace">{{ $log->character_count }} chars</div>
                                            </td>
                                            <td class="text-center">
                                                <code class="text-dark small text-wrap d-block" style="max-width: 200px; max-height: 60px; overflow-y: auto; text-align: left;">
                                                    {{ $log->response ?? 'N/A' }}
                                                </code>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $log->status === 'Success' ? 'bg-success' : 'bg-danger' }} rounded-pill px-2.5 py-1">
                                                    @if($log->status === 'Success')
                                                        <i class="fas fa-check-circle me-1"></i> Sent
                                                    @else
                                                        <i class="fas fa-exclamation-triangle me-1"></i> Failed
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="text-end text-muted small font-monospace">
                                                {{ $log->created_at->format('M d, Y') }}<br>
                                                {{ $log->created_at->format('g:i A') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="fas fa-sms mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                                                    <p class="mb-0">No SMS transmissions logged yet</p>
                                                    <small>When your server triggers automated notifications, logs will appear here.</small>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 px-4 pb-4">
                        <div class="d-flex justify-content-center">
                            {{ $logs->links('template.paginationlinks') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-gradient-dark {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
        }
        .text-muted-light {
            color: rgba(255, 255, 255, 0.6) !important;
        }
        .progress-lh {
            background-color: rgba(0, 0, 0, 0.05);
            border-radius: 10px;
            overflow: hidden;
        }
        .table-responsive {
            max-width: calc(100vw - 100px);
        }
    </style>
@endsection
