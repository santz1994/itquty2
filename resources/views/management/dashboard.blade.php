@extends('layouts.app')

@section('main-content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Management Dashboard
            <small>Strategic overview and KPI metrics</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('/home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Management Dashboard</li>
        </ol>
    </section>

    <section class="content">
        <!-- Overview Info boxes -->
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-ticket"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Today's Tickets</span>
                        <span class="info-box-number">{{ $overview['total_tickets_today'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-calendar"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">This Month</span>
                        <span class="info-box-number">{{ $overview['total_tickets_month'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Overdue</span>
                        <span class="info-box-number">{{ $overview['overdue_tickets'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-question-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Unassigned</span>
                        <span class="info-box-number">{{ $overview['unassigned_tickets'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second row of info boxes -->
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-desktop"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Assets</span>
                        <span class="info-box-number">{{ $overview['total_assets'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-purple"><i class="fa fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Active Admins</span>
                        <span class="info-box-number">{{ $overview['active_admins'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-orange"><i class="fa fa-pie-chart"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">SLA Compliance</span>
                        <span class="info-box-number">{{ $sla_compliance['compliance_rate'] ?? 0 }}%</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-teal"><i class="fa fa-cogs"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Assets In Use</span>
                        <span class="info-box-number">{{ $asset_overview['in_use'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Performance -->
        <div class="row">
            <!-- Ticket Trends Chart -->
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Ticket Trends (Last 30 Days)</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <canvas id="ticketTrendsChart" width="400" height="150"></canvas>
                    </div>
                </div>
            </div>

            <!-- Asset Overview -->
            <div class="col-md-4">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Asset Status Distribution</h3>
                    </div>
                    <div class="box-body">
                        <canvas id="assetStatusChart" width="200" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Performance Summary -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Top Performing Admins</h3>
                        <div class="box-tools pull-right">
                            <a href="{{ route('management.admin-performance') }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Admin</th>
                                        <th>Role</th>
                                        <th>Tickets Assigned</th>
                                        <th>Tickets Resolved</th>
                                        <th>Resolution Rate</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($admin_performance) && count($admin_performance) > 0)
                                        @foreach(array_slice($admin_performance, 0, 5) as $performanceData)
                                            @php
                                                $admin = $performanceData['admin'];
                                                $metrics = $performanceData['metrics'] ?? [];
                                            @endphp
                                            <tr>
                                                <td>
                                                    <img src="{{ asset('img/avatar.png') }}" class="img-circle" style="width: 30px; height: 30px;">
                                                    {{ $admin->name }}
                                                </td>
                                                <td>
                                                    @if($admin->roles->count() > 0)
                                                        @foreach($admin->roles as $role)
                                                            <span class="label label-primary">{{ $role->name }}</span>
                                                        @endforeach
                                                    @else
                                                        <span class="label label-default">No Role</span>
                                                    @endif
                                                </td>
                                                <td>{{ $metrics['total_assigned'] ?? 0 }}</td>
                                                <td>{{ $metrics['total_completed'] ?? 0 }}</td>
                                                <td>
                                                    @php
                                                        $rate = $metrics['completion_rate'] ?? 0;
                                                    @endphp
                                                    <span class="label @if($rate >= 80) label-success @elseif($rate >= 60) label-warning @else label-danger @endif">
                                                        {{ number_format($rate, 1) }}%
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($admin->adminOnlineStatus && $admin->adminOnlineStatus->is_online)
                                                        <span class="label label-success">Online</span>
                                                    @else
                                                        <span class="label label-default">Offline</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">No admin performance data available</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">Quick Actions</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-6">
                                <a href="{{ route('management.admin-performance') }}" class="btn btn-app">
                                    <i class="fa fa-users"></i> Admin Performance
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <a href="{{ route('management.ticket-reports') }}" class="btn btn-app">
                                    <i class="fa fa-ticket"></i> Ticket Reports
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <a href="{{ route('management.asset-reports') }}" class="btn btn-app">
                                    <i class="fa fa-desktop"></i> Asset Reports
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <a href="{{ url('/system/logs') }}" class="btn btn-app">
                                    <i class="fa fa-file-text"></i> System Logs
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script src="{{ asset('plugins/chartjs/Chart.min.js') }}"></script>

<script>
$(document).ready(function() {
    // Ticket Trends Chart
    var trendsCtx = document.getElementById('ticketTrendsChart');
    if (trendsCtx) {
        var ticketTrendsChart = new Chart(trendsCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($ticket_trends->pluck('date')->map(function($date) { return \Carbon\Carbon::parse($date)->format('M d'); })) !!},
                datasets: [{
                    label: 'Created',
                    data: {!! json_encode($ticket_trends->pluck('created')) !!},
                    borderColor: 'rgb(60, 141, 188)',
                    backgroundColor: 'rgba(60, 141, 188, 0.1)',
                    tension: 0.1
                }, {
                    label: 'Resolved',
                    data: {!! json_encode($ticket_trends->pluck('resolved')) !!},
                    borderColor: 'rgb(0, 166, 90)',
                    backgroundColor: 'rgba(0, 166, 90, 0.1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Asset Status Chart
    var assetCtx = document.getElementById('assetStatusChart');
    if (assetCtx) {
        var assetChart = new Chart(assetCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['In Use', 'In Stock', 'In Repair', 'Disposed'],
                datasets: [{
                    data: [
                        {{ $asset_overview['in_use'] ?? 0 }},
                        {{ $asset_overview['in_stock'] ?? 0 }},
                        {{ $asset_overview['in_repair'] ?? 0 }},
                        {{ $asset_overview['disposed'] ?? 0 }}
                    ],
                    backgroundColor: [
                        '#00a65a',
                        '#3c8dbc',
                        '#f39c12',
                        '#dd4b39'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
});
</script>
@endsection