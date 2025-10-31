@extends('layouts.app')

@section('main-content')
    @include('components.page-header', [
        'title' => 'View Daily Activity',
        'subtitle' => 'Detailed information about this activity',
        'icon' => 'fa-eye',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => url('/home'), 'icon' => 'dashboard'],
            ['label' => 'Daily Activities', 'url' => route('daily-activities.index')],
            ['label' => 'Activity Details']
        ],
        'actions' => '<a href="' . route('daily-activities.edit', $dailyActivity->id) . '" class="btn btn-warning">
                        <i class="fa fa-edit"></i> Edit Activity
                      </a>
                      <button onclick="window.print()" class="btn btn-default">
                        <i class="fa fa-print"></i> Print
                      </button>'
    ])

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <i class="icon fa fa-check"></i> {{ session('success') }}
        </div>
    @endif

    <section class="content">
        <div class="row">
            {{-- Main Content: 8 columns --}}
            <div class="col-md-8">
                {{-- Activity Overview --}}
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-info-circle"></i> Activity Overview
                        </h3>
                        <div class="box-tools pull-right">
                            <span class="badge bg-blue">ID: #{{ $dailyActivity->id }}</span>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-aqua"><i class="fa fa-calendar"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Activity Date</span>
                                        <span class="info-box-number">{{ $dailyActivity->activity_date->format('M d, Y') }}</span>
                                        <small>{{ $dailyActivity->activity_date->format('l') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-green"><i class="fa fa-tag"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Activity Type</span>
                                        <span class="info-box-number" style="font-size: 16px;">
                                            {{ ucwords(str_replace('_', ' ', $dailyActivity->activity_type)) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-yellow"><i class="fa fa-clock-o"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Duration</span>
                                        <span class="info-box-number">
                                            {{ $dailyActivity->duration_minutes ?? 'N/A' }} minutes
                                        </span>
                                        <small>
                                            @if($dailyActivity->duration_minutes)
                                                ≈ {{ number_format($dailyActivity->duration_minutes / 60, 1) }} hours
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-red"><i class="fa fa-user"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Logged By</span>
                                        <span class="info-box-number" style="font-size: 16px;">
                                            {{ $dailyActivity->user->name ?? 'Unknown' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <fieldset style="margin-top: 20px;">
                            <legend><i class="fa fa-file-text"></i> Description</legend>
                            <div style="padding: 15px; background: #f9f9f9; border-left: 3px solid #3c8dbc; border-radius: 3px;">
                                <p style="margin: 0; line-height: 1.8;">{{ $dailyActivity->description }}</p>
                            </div>
                        </fieldset>
                    </div>
                </div>

                {{-- Time Tracking Details --}}
                @if($dailyActivity->start_time || $dailyActivity->end_time || $dailyActivity->location)
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                <i class="fa fa-clock-o"></i> Time Tracking Details
                            </h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered table-striped">
                                <tbody>
                                    @if($dailyActivity->start_time)
                                        <tr>
                                            <th style="width: 200px;"><i class="fa fa-play-circle"></i> Start Time</th>
                                            <td>{{ $dailyActivity->start_time->format('h:i A') }}</td>
                                        </tr>
                                    @endif
                                    @if($dailyActivity->end_time)
                                        <tr>
                                            <th><i class="fa fa-stop-circle"></i> End Time</th>
                                            <td>{{ $dailyActivity->end_time->format('h:i A') }}</td>
                                        </tr>
                                    @endif
                                    @if($dailyActivity->start_time && $dailyActivity->end_time)
                                        <tr>
                                            <th><i class="fa fa-hourglass-half"></i> Calculated Duration</th>
                                            <td>
                                                @php
                                                    $duration = $dailyActivity->start_time->diff($dailyActivity->end_time);
                                                    $hours = $duration->h;
                                                    $minutes = $duration->i;
                                                @endphp
                                                <strong>{{ $hours }} hours {{ $minutes }} minutes</strong>
                                            </td>
                                        </tr>
                                    @endif
                                    @if($dailyActivity->location)
                                        <tr>
                                            <th><i class="fa fa-map-marker"></i> Work Location</th>
                                            <td>
                                                <span class="label label-info">
                                                    {{ ucwords(str_replace('_', ' ', $dailyActivity->location)) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Related Items --}}
                @if($dailyActivity->ticket_id || ($dailyActivity->notes && strlen($dailyActivity->notes) > 0))
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                <i class="fa fa-link"></i> Related Information
                            </h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered">
                                <tbody>
                                    @if($dailyActivity->ticket_id)
                                        <tr>
                                            <th style="width: 200px;"><i class="fa fa-ticket"></i> Related Ticket</th>
                                            <td>
                                                @if(isset($dailyActivity->ticket))
                                                    <a href="{{ route('tickets.show', $dailyActivity->ticket_id) }}" class="btn btn-sm btn-primary">
                                                        <i class="fa fa-external-link"></i> Ticket #{{ $dailyActivity->ticket_id }}
                                                    </a>
                                                    <br><small style="margin-top: 5px; display: block;">
                                                        {{ \Illuminate\Support\Str::limit($dailyActivity->ticket->subject ?? '', 80) }}
                                                    </small>
                                                @else
                                                    Ticket #{{ $dailyActivity->ticket_id }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                    @if($dailyActivity->notes && strlen($dailyActivity->notes) > 0)
                                        <tr>
                                            <th><i class="fa fa-sticky-note"></i> Additional Notes</th>
                                            <td>{{ $dailyActivity->notes }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Activity Metadata --}}
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-info"></i> Activity Metadata
                        </h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-condensed">
                            <tbody>
                                <tr>
                                    <th style="width: 200px;"><i class="fa fa-hashtag"></i> Activity ID</th>
                                    <td><code>#{{ $dailyActivity->id }}</code></td>
                                </tr>
                                <tr>
                                    <th><i class="fa fa-plus-circle"></i> Created At</th>
                                    <td>
                                        {{ $dailyActivity->created_at->format('M d, Y h:i A') }}
                                        <small class="text-muted">({{ $dailyActivity->created_at->diffForHumans() }})</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th><i class="fa fa-edit"></i> Last Updated</th>
                                    <td>
                                        {{ $dailyActivity->updated_at->format('M d, Y h:i A') }}
                                        <small class="text-muted">({{ $dailyActivity->updated_at->diffForHumans() }})</small>
                                    </td>
                                </tr>
                                @if(isset($dailyActivity->user))
                                    <tr>
                                        <th><i class="fa fa-user"></i> Logged By</th>
                                        <td>
                                            <strong>{{ $dailyActivity->user->name }}</strong>
                                            <br><small>{{ $dailyActivity->user->email }}</small>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Sidebar: 4 columns --}}
            <div class="col-md-4">
                {{-- Quick Actions --}}
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-bolt"></i> Quick Actions</h3>
                    </div>
                    <div class="box-body">
                        <a href="{{ route('daily-activities.index') }}" class="btn btn-default btn-block">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                        <a href="{{ route('daily-activities.edit', $dailyActivity->id) }}" class="btn btn-warning btn-block">
                            <i class="fa fa-edit"></i> Edit Activity
                        </a>
                        <a href="{{ route('daily-activities.create') }}" class="btn btn-primary btn-block">
                            <i class="fa fa-plus"></i> Add New Activity
                        </a>
                        <button onclick="window.print()" class="btn btn-info btn-block">
                            <i class="fa fa-print"></i> Print Details
                        </button>
                        <hr>
                        <form action="{{ route('daily-activities.destroy', $dailyActivity->id) }}" 
                              method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this activity? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fa fa-trash"></i> Delete Activity
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Activity Stats --}}
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-bar-chart"></i> Activity Statistics</h3>
                    </div>
                    <div class="box-body">
                        <div class="info-box bg-aqua">
                            <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Time Spent</span>
                                <span class="info-box-number">
                                    {{ $dailyActivity->duration_minutes ?? 0 }} min
                                </span>
                            </div>
                        </div>

                        <div class="info-box bg-green">
                            <span class="info-box-icon"><i class="fa fa-calendar"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Days Ago</span>
                                <span class="info-box-number">
                                    {{ $dailyActivity->activity_date->diffInDays(now()) }}
                                </span>
                            </div>
                        </div>

                        @if($dailyActivity->ticket_id)
                            <div class="info-box bg-yellow">
                                <span class="info-box-icon"><i class="fa fa-ticket"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Linked to Ticket</span>
                                    <span class="info-box-number">
                                        #{{ $dailyActivity->ticket_id }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Activity Timeline --}}
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-history"></i> Activity Timeline</h3>
                    </div>
                    <div class="box-body">
                        <ul class="timeline timeline-inverse">
                            <li class="time-label">
                                <span class="bg-red">
                                    {{ $dailyActivity->activity_date->format('M d, Y') }}
                                </span>
                            </li>
                            @if($dailyActivity->start_time)
                                <li>
                                    <i class="fa fa-play-circle bg-blue"></i>
                                    <div class="timeline-item">
                                        <h3 class="timeline-header">Activity Started</h3>
                                        <div class="timeline-body">
                                            Started at {{ $dailyActivity->start_time->format('h:i A') }}
                                        </div>
                                    </div>
                                </li>
                            @endif
                            @if($dailyActivity->end_time)
                                <li>
                                    <i class="fa fa-stop-circle bg-green"></i>
                                    <div class="timeline-item">
                                        <h3 class="timeline-header">Activity Completed</h3>
                                        <div class="timeline-body">
                                            Finished at {{ $dailyActivity->end_time->format('h:i A') }}
                                        </div>
                                    </div>
                                </li>
                            @endif
                            <li>
                                <i class="fa fa-save bg-gray"></i>
                                <div class="timeline-item">
                                    <h3 class="timeline-header">Activity Logged</h3>
                                    <div class="timeline-body">
                                        Logged by {{ $dailyActivity->user->name ?? 'System' }} at 
                                        {{ $dailyActivity->created_at->format('h:i A') }}
                                    </div>
                                </div>
                            </li>
                            <li>
                                <i class="fa fa-clock-o bg-gray"></i>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Export Options --}}
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-download"></i> Export Options</h3>
                    </div>
                    <div class="box-body">
                        <p style="font-size: 12px; color: #666; margin-bottom: 10px;">
                            Export this activity data in different formats:
                        </p>
                        <button onclick="exportActivity('pdf')" class="btn btn-danger btn-block">
                            <i class="fa fa-file-pdf-o"></i> Export as PDF
                        </button>
                        <button onclick="exportActivity('excel')" class="btn btn-success btn-block">
                            <i class="fa fa-file-excel-o"></i> Export as Excel
                        </button>
                        <button onclick="exportActivity('json')" class="btn btn-info btn-block">
                            <i class="fa fa-code"></i> Export as JSON
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

@push('scripts')
<script>
$(document).ready(function() {
    // Export functionality
    window.exportActivity = function(format) {
        const activityId = {{ $dailyActivity->id }};
        const exportUrl = `/daily-activities/${activityId}/export/${format}`;
        
        // Show loading message
        $('body').append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
        
        // Simulate export (replace with actual endpoint)
        setTimeout(function() {
            $('.overlay').remove();
            alert('Export as ' + format.toUpperCase() + ' is being prepared. This feature will download the file.');
            // In production, redirect to: window.location.href = exportUrl;
        }, 1000);
    };
    
    // Print styles
    const printStyles = `
        @media print {
            .box-tools, .box-header .btn, .sidebar, .btn, form, .overlay, .no-print {
                display: none !important;
            }
            .col-md-8 {
                width: 100% !important;
            }
            .box {
                border: 1px solid #ddd;
                page-break-inside: avoid;
            }
        }
    `;
    
    if (!$('#print-styles').length) {
        $('<style id="print-styles">' + printStyles + '</style>').appendTo('head');
    }
    
    // Tooltip initialization
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endpush

@push('styles')
<style>
.timeline {
    position: relative;
    margin: 0 0 30px 0;
    padding: 0;
    list-style: none;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 4px;
    background: #ddd;
    left: 31px;
    margin: 0;
    border-radius: 2px;
}

.timeline > li {
    position: relative;
    margin-right: 10px;
    margin-bottom: 15px;
}

.timeline > li > .timeline-item {
    margin-top: 0;
    background: #fff;
    color: #444;
    margin-left: 60px;
    margin-right: 15px;
    padding: 10px;
    position: relative;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    border-radius: 3px;
}

.timeline > li > .fa,
.timeline > li > .glyphicon,
.timeline > li > .ion {
    width: 30px;
    height: 30px;
    font-size: 14px;
    line-height: 30px;
    position: absolute;
    color: #666;
    background: #d2d6de;
    border-radius: 50%;
    text-align: center;
    left: 18px;
    top: 0;
}

.timeline > .time-label > span {
    font-weight: 600;
    padding: 5px;
    display: inline-block;
    background-color: #fff;
    border-radius: 4px;
}

.timeline-header {
    margin: 0;
    color: #555;
    border-bottom: 1px solid #f4f4f4;
    padding-bottom: 5px;
    font-size: 14px;
    line-height: 1.1;
}

.timeline-body {
    padding-top: 10px;
    font-size: 13px;
}

.metadata-alert {
    margin-bottom: 20px;
}
</style>
@endpush
@endsection
