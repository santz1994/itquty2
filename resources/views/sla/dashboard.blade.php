@extends('layouts.app')

@section('main-content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-chart-line"></i> SLA Dashboard</h2>
                <div>
                    <a href="{{ route('sla.index') }}" class="btn btn-secondary">
                        <i class="fas fa-cog"></i> Manage Policies
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Info -->
    <div class="row mb-3">
        <div class="col-md-12">
            @php
                $driver = config('database.default');
                $dbHost = config("database.connections.{${"driver"}}.host") ?? config("database.connections.{$driver}.host");
                $dbName = config("database.connections.{$driver}.database");
            @endphp
            <div class="card border-info">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Page Info</h5>
                        <small class="text-muted">App: {{ config('app.name') }} • Env: {{ config('app.env') }} • Laravel: {{ app()->version() }} • PHP: {{ PHP_VERSION }}</small>
                    </div>
                    <div class="text-right">
                        <div><strong>DB:</strong> {{ $driver }} / {{ $dbName }}</div>
                        <div class="text-muted small">Host: {{ $dbHost ?? 'n/a' }}</div>
                        <div class="mt-2"><small>Logged in as: {{ optional(Auth::user())->name ?? 'Guest' }}</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('sla.dashboard') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="start_date" 
                                           name="start_date" 
                                           value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="end_date" 
                                           name="end_date" 
                                           value="{{ request('end_date', now()->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="priority_id">Priority</label>
                                    <select class="form-control" id="priority_id" name="priority_id">
                                        <option value="">All Priorities</option>
                                        @foreach($priorities as $priority)
                                            <option value="{{ $priority->id }}" 
                                                    {{ request('priority_id') == $priority->id ? 'selected' : '' }}>
                                                {{ $priority->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="assigned_to">Assigned To</label>
                                    <select class="form-control" id="assigned_to" name="assigned_to">
                                        <option value="">All Users</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" 
                                                    {{ request('assigned_to') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Apply Filters
                                </button>
                                <a href="{{ route('sla.dashboard') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Total Tickets</h5>
                            <h2 class="mt-2 mb-0">{{ $metrics['total_tickets'] }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-ticket-alt fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">SLA Met</h5>
                            <h2 class="mt-2 mb-0">{{ $metrics['sla_met'] }}</h2>
                            <small>{{ $metrics['sla_compliance_rate'] }}% compliance</small>
                        </div>
                        <div>
                            <i class="fas fa-check-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">SLA Breached</h5>
                            <h2 class="mt-2 mb-0">{{ $metrics['sla_breached'] }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-exclamation-triangle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Critical Tickets</h5>
                            <h2 class="mt-2 mb-0">{{ $metrics['critical_tickets'] }}</h2>
                            <small>Needs attention</small>
                        </div>
                        <div>
                            <i class="fas fa-fire fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Small charts -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">SLA Compliance</h5>
                    <div class="chart-card">
                        <canvas id="slaComplianceChart" aria-label="SLA compliance chart" role="img"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tickets by Priority</h5>
                    <div class="chart-card">
                        <canvas id="ticketsPriorityChart" aria-label="Tickets by priority chart" role="img"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Average Times -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-reply"></i> Average First Response Time
                    </h5>
                </div>
                <div class="card-body">
                    <h2 class="text-primary">
                        {{ number_format($metrics['avg_response_time_hours'], 1) }} hrs
                    </h2>
                    <p class="text-muted mb-0">
                        Time from ticket creation to first response
                        <br><small>({{ number_format($metrics['avg_response_time_minutes'], 0) }} minutes)</small>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-check-circle"></i> Average Resolution Time
                    </h5>
                </div>
                <div class="card-body">
                    <h2 class="text-success">
                        {{ number_format($metrics['avg_resolution_time_hours'], 1) }} hrs
                    </h2>
                    <p class="text-muted mb-0">
                        Time from ticket creation to resolution
                        <br><small>({{ number_format($metrics['avg_resolution_time_minutes'], 0) }} minutes)</small>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Breached Tickets -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle text-danger"></i> Breached SLA Tickets
                    </h5>
                </div>
                <div class="card-body">
                    @if($breachedTickets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Subject</th>
                                        <th>Priority</th>
                                        <th>Assigned To</th>
                                        <th>Created</th>
                                        <th>SLA Due</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($breachedTickets as $ticket)
                                        <tr>
                                            <td><strong>#{{ $ticket->id }}</strong></td>
                                            <td>{{ \Illuminate\Support\Str::limit($ticket->subject, 50) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $ticket->priority->color ?? 'secondary' }}">
                                                    {{ $ticket->priority->name ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $ticket->assignedTo->name ?? 'Unassigned' }}
                                            </td>
                                            <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                @if($ticket->sla_due)
                                                    <span class="text-danger">
                                                        <i class="fas fa-clock"></i>
                                                        {{ $ticket->sla_due->format('Y-m-d H:i') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">No SLA</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-times-circle"></i> Breached
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('tickets.show', $ticket->id) }}" 
                                                   class="btn btn-sm btn-primary"
                                                   target="_blank">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if(method_exists($breachedTickets, 'hasPages') && $breachedTickets->hasPages())
                            <div class="mt-3">
                                {{ $breachedTickets->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                            <p>No breached SLA tickets found! Great work!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Critical Tickets (At Risk) -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-fire text-warning"></i> Critical Tickets (At Risk of SLA Breach)
                    </h5>
                </div>
                <div class="card-body">
                    @if($criticalTickets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Subject</th>
                                        <th>Priority</th>
                                        <th>Assigned To</th>
                                        <th>Created</th>
                                        <th>SLA Due</th>
                                        <th>Time Remaining</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($criticalTickets as $ticket)
                                        @php
                                            $slaStatus = app(\App\Services\SlaTrackingService::class)->getSlaStatus($ticket);
                                        @endphp
                                        <tr>
                                            <td><strong>#{{ $ticket->id }}</strong></td>
                                            <td>{{ \Illuminate\Support\Str::limit($ticket->subject, 50) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $ticket->priority->color ?? 'secondary' }}">
                                                    {{ $ticket->priority->name ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $ticket->assignedTo->name ?? 'Unassigned' }}
                                            </td>
                                            <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                @if($ticket->sla_due)
                                                    <span class="text-warning">
                                                        <i class="fas fa-clock"></i>
                                                        {{ $ticket->sla_due->format('Y-m-d H:i') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">No SLA</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($slaStatus && isset($slaStatus['percentage_remaining']))
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar bg-{{ $slaStatus['color'] }}" 
                                                             role="progressbar" 
                                                             style="width: {{ $slaStatus['percentage_remaining'] }}%"
                                                             aria-valuenow="{{ $slaStatus['percentage_remaining'] }}" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                            {{ round($slaStatus['percentage_remaining'], 1) }}%
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $slaStatus['color'] ?? 'secondary' }}">
                                                    <i class="fas fa-{{ $slaStatus['icon'] ?? 'question' }}"></i> 
                                                    {{ ucfirst(str_replace('_', ' ', $slaStatus['status'] ?? 'unknown')) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('tickets.show', $ticket->id) }}" 
                                                   class="btn btn-sm btn-primary"
                                                   target="_blank">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if(method_exists($criticalTickets, 'hasPages') && $criticalTickets->hasPages())
                            <div class="mt-3">
                                {{ $criticalTickets->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-thumbs-up fa-3x mb-3 text-success"></i>
                            <p>No critical tickets at risk! Keep up the good work!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- SLA Policy Summary -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clipboard-list"></i> Active SLA Policies
                    </h5>
                </div>
                <div class="card-body">
                    @if($activePolicies->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Priority</th>
                                        <th>Response Time</th>
                                        <th>Resolution Time</th>
                                        <th>Business Hours</th>
                                        <th>Escalation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activePolicies as $policy)
                                        <tr>
                                            <td>
                                                <span class="badge badge-{{ $policy->priority->color ?? 'secondary' }}">
                                                    {{ $policy->priority->name ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>{{ $policy->response_time }} min</td>
                                            <td>{{ $policy->resolution_time }} min</td>
                                            <td>
                                                @if($policy->business_hours_only)
                                                    <i class="fas fa-check text-success"></i> Yes
                                                @else
                                                    <i class="fas fa-times text-danger"></i> No (24/7)
                                                @endif
                                            </td>
                                            <td>
                                                @if($policy->escalation_time)
                                                    {{ $policy->escalation_time }} min
                                                    @if($policy->escalateToUser)
                                                        → {{ $policy->escalateToUser->name }}
                                                    @endif
                                                @else
                                                    <span class="text-muted">Not configured</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                            <p>No active SLA policies found. Please create policies to track SLAs.</p>
                            <a href="{{ route('sla.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create SLA Policy
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.opacity-50 {
    opacity: 0.5;
}
.card-body h2 {
    font-size: 2.5rem;
    font-weight: bold;
}
/* improved table responsiveness and minor visual tweaks */
.table-responsive { overflow-x: auto; }
.card .card-title { font-weight: 600; }
.badge { font-size: .9rem; }
.chart-card { height: 220px; max-height: 320px; position: relative; }
.chart-card canvas { width: 100% !important; height: 220px !important; display: block; }
</style>
@endsection

@section('scripts')
<!-- Chart.js for small dashboard charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    try {
        const slaMet = {{ json_encode($metrics['sla_met'] ?? 0) }};
        const slaBreached = {{ json_encode($metrics['sla_breached'] ?? 0) }};

        const slaCtx = document.getElementById('slaComplianceChart');
        if (slaCtx && slaCtx.getContext) {
            new Chart(slaCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['SLA Met','SLA Breached'],
                    datasets: [{ data: [slaMet, slaBreached], backgroundColor: ['#28a745','#dc3545'] }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
            });
        }

        const priorities = {!! json_encode($priorities->pluck('name')->toArray() ?? []) !!};
        const ticketsByPriority = {!! json_encode($metrics['tickets_by_priority'] ?? []) !!};
        const prCtx = document.getElementById('ticketsPriorityChart');
        if (prCtx && prCtx.getContext && Array.isArray(priorities) && priorities.length) {
            new Chart(prCtx.getContext('2d'), {
                type: 'bar',
                data: { labels: priorities, datasets: [{ label: 'Tickets', data: ticketsByPriority, backgroundColor: '#007bff' }] },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
            });
        }
    } catch (e) {
        console.warn('SLA dashboard init error', e);
    }
});
</script>
@endsection
