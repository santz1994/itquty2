@extends('layouts.app')

@section('main-content')
<!-- Dashboard Overview -->
<div class="row">
    <!-- Quick Stats -->
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3>{{ $stats['open_tickets'] ?? 0 }}</h3>
                <p>Open Tickets</p>
            </div>
            <div class="icon">
                <i class="fa fa-ticket"></i>
            </div>
            <a href="{{ url('/tickets') }}" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
            <div class="inner">
                <h3>{{ $stats['overdue_tickets'] ?? 0 }}</h3>
                <p>Overdue Tickets</p>
            </div>
            <div class="icon">
                <i class="fa fa-exclamation-triangle"></i>
            </div>
            <a href="{{ url('/tickets?filter=overdue') }}" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner">
                <h3>{{ $stats['total_assets'] ?? 0 }}</h3>
                <p>Total Assets</p>
            </div>
            <div class="icon">
                <i class="fa fa-tags"></i>
            </div>
            <a href="{{ url('/assets') }}" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3>{{ $stats['maintenance_due'] ?? 0 }}</h3>
                <p>Maintenance Due</p>
            </div>
            <div class="icon">
                <i class="fa fa-wrench"></i>
            </div>
            <a href="{{ url('/assets?filter=maintenance_due') }}" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Main Dashboard Content -->
<div class="row">
    <!-- Recent Tickets -->
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-ticket"></i> Recent Tickets
                </h3>
                <div class="box-tools pull-right">
                    <a href="{{ url('/tickets/create') }}" class="btn btn-primary btn-xs">
                        <i class="fa fa-plus"></i> New Ticket
                    </a>
                </div>
            </div>
            <div class="box-body">
                @if(isset($recentTickets) && $recentTickets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-condensed">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Subject</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>SLA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTickets as $ticket)
                                <tr>
                                    <td>
                                        <a href="{{ route('tickets.show', $ticket->id) }}">
                                            {{ $ticket->ticket_code }}
                                        </a>
                                    </td>
                                    <td>{{ \Illuminate\Support\Str::limit($ticket->subject, 30) }}</td>
                                    <td>
                                        @if($ticket->ticket_priority)
                                            <span class="label label-{{ $ticket->ticket_priority->color ?? 'default' }}">
                                                {{ $ticket->ticket_priority->priority }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ticket->ticket_status)
                                            <span class="label label-{{ $ticket->ticket_status->color ?? 'default' }}">
                                                {{ $ticket->ticket_status->status }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ticket->sla_due)
                                            @php
                                                $now = now();
                                                $slaClass = 'success';
                                                if ($ticket->sla_due->isPast()) $slaClass = 'danger';
                                                elseif ($ticket->sla_due->diffInHours($now) <= 2) $slaClass = 'warning';
                                            @endphp
                                            <small class="label label-{{ $slaClass }}">
                                                {{ $ticket->sla_due->diffForHumans() }}
                                            </small>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">No recent tickets</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Asset Status -->
    <div class="col-md-6">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-desktop"></i> Asset Status Overview
                </h3>
                <div class="box-tools pull-right">
                    <a href="{{ url('/assets') }}" class="btn btn-success btn-xs">
                        <i class="fa fa-list"></i> View All
                    </a>
                </div>
            </div>
            <div class="box-body">
                @if(isset($assetStats))
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Active</span>
                                    <span class="info-box-number">{{ $assetStats['active'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-yellow"><i class="fa fa-wrench"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">In Repair</span>
                                    <span class="info-box-number">{{ $assetStats['in_repair'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-blue"><i class="fa fa-archive"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">In Stock</span>
                                    <span class="info-box-number">{{ $assetStats['in_stock'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-red"><i class="fa fa-trash"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Disposed</span>
                                    <span class="info-box-number">{{ $assetStats['disposed'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Daily Activities & Asset Maintenance -->
<div class="row">
    <!-- Today's Activities -->
    <div class="col-md-8">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-calendar-check-o"></i> Today's Activities
                </h3>
                <div class="box-tools pull-right">
                    <a href="{{ route('daily-activities.create') }}" class="btn btn-info btn-xs">
                        <i class="fa fa-plus"></i> Add Activity
                    </a>
                    <a href="{{ route('daily-activities.index') }}" class="btn btn-default btn-xs">
                        <i class="fa fa-list"></i> View All
                    </a>
                </div>
            </div>
            <div class="box-body">
                @if(isset($todayActivities) && $todayActivities->count() > 0)
                    <div class="timeline">
                        @foreach($todayActivities as $activity)
                        <div class="time-label">
                            <span class="bg-blue">{{ $activity->created_at->format('H:i') }}</span>
                        </div>
                        <div>
                            <i class="fa fa-{{ $activity->type === 'manual' ? 'edit' : 'cogs' }} bg-{{ $activity->type === 'manual' ? 'blue' : 'green' }}"></i>
                            <div class="timeline-item">
                                <span class="time">
                                    <i class="fa fa-clock-o"></i> {{ $activity->created_at->diffForHumans() }}
                                </span>
                                <h3 class="timeline-header">
                                    <strong>{{ $activity->user->name }}</strong>
                                    @if($activity->ticket)
                                        - <a href="{{ route('tickets.show', $activity->ticket->id) }}">{{ $activity->ticket->ticket_code }}</a>
                                    @endif
                                </h3>
                                <div class="timeline-body">
                                    {{ \Illuminate\Support\Str::limit($activity->description, 150) }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <div>
                            <i class="fa fa-clock-o bg-gray"></i>
                        </div>
                    </div>
                @else
                    <div class="text-center">
                        <p class="text-muted">No activities logged today</p>
                        <a href="{{ route('daily-activities.create') }}" class="btn btn-info">
                            <i class="fa fa-plus"></i> Log Your First Activity
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Alerts & Notifications -->
    <div class="col-md-4">
        <!-- SLA Alerts -->
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-exclamation-triangle"></i> SLA Alerts
                </h3>
            </div>
            <div class="box-body">
                @if(isset($slaAlerts) && $slaAlerts->count() > 0)
                    @foreach($slaAlerts as $alert)
                    <div class="alert alert-{{ $alert->sla_status_color }} alert-dismissible">
                        <h4>{{ $alert->ticket_code }}</h4>
                        <p>{{ \Illuminate\Support\Str::limit($alert->subject, 50) }}</p>
                        <small>Due: {{ $alert->sla_due->format('d/m H:i') }}</small>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted">No SLA alerts</p>
                @endif
            </div>
        </div>

        <!-- Asset Maintenance -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-wrench"></i> Maintenance Due
                </h3>
            </div>
            <div class="box-body">
                @if(isset($maintenanceDue) && $maintenanceDue->count() > 0)
                    @foreach($maintenanceDue as $asset)
                    <div class="media">
                        <div class="media-left">
                            <i class="fa fa-desktop fa-2x text-yellow"></i>
                        </div>
                        <div class="media-body">
                            <h5 class="media-heading">{{ $asset->asset_tag }}</h5>
                            <p>{{ $asset->model->name ?? 'Unknown Model' }}</p>
                            <small class="text-muted">Last maintenance: {{ $asset->last_maintenance ?? 'Never' }}</small>
                        </div>
                    </div>
                    <hr>
                    @endforeach
                @else
                    <p class="text-muted">No maintenance due</p>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-flash"></i> Quick Actions
                </h3>
            </div>
            <div class="box-body">
                <div class="btn-group-vertical btn-block">
                    <a href="{{ url('/tickets/create') }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Create Ticket
                    </a>
                    <a href="{{ url('/assets/create') }}" class="btn btn-success">
                        <i class="fa fa-plus"></i> Add Asset
                    </a>
                    <a href="{{ route('daily-activities.create') }}" class="btn btn-info">
                        <i class="fa fa-plus"></i> Log Activity
                    </a>
                    <a href="{{ url('/reports') }}" class="btn btn-warning">
                        <i class="fa fa-chart-bar"></i> View Reports
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Integration Status -->
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-cogs"></i> System Integration Status
                </h3>
                <div class="box-tools pull-right">
                    <a href="/comprehensive-test.html" class="btn btn-default btn-xs">
                        <i class="fa fa-check"></i> Run System Test
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-ticket"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Ticketing System</span>
                                <span class="info-box-number">
                                    <i class="fa fa-check text-green"></i> Active
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-tags"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Asset Management</span>
                                <span class="info-box-number">
                                    <i class="fa fa-check text-green"></i> Active
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-calendar"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Daily Activities</span>
                                <span class="info-box-number">
                                    <i class="fa fa-check text-green"></i> Active
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-qrcode"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">QR Integration</span>
                                <span class="info-box-number">
                                    <i class="fa fa-check text-green"></i> Active
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
<script>
$(document).ready(function() {
    // Auto refresh dashboard every 5 minutes
    setTimeout(function() {
        location.reload();
    }, 300000);
    
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Real-time clock
    function updateClock() {
        var now = new Date();
        var timeString = now.toLocaleTimeString();
        $('#current-time').text(timeString);
    }
    
    // Update clock every second if element exists
    if ($('#current-time').length) {
        setInterval(updateClock, 1000);
        updateClock(); // Initial call
    }
});
</script>
@endsection

