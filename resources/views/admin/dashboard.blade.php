@extends('layouts.app')

@section('main-content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Admin Dashboard
            <small>System overview and quick actions</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('/home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Admin Dashboard</li>
        </ol>
    </section>

    <section class="content">
        <!-- Info boxes -->
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Users</span>
                        <span class="info-box-number">{{ $stats['total_users'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-desktop"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Assets</span>
                        <span class="info-box-number">{{ $stats['total_assets'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-ticket"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Active Tickets</span>
                        <span class="info-box-number">{{ $stats['active_tickets'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pending Requests</span>
                        <span class="info-box-number">{{ $stats['pending_requests'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Status Alert -->
        <div class="row">
            <div class="col-md-12">
                @if(auth()->user()->email === 'daniel@quty.co.id')
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-shield"></i> Full Administrative Access</h4>
                    You have unrestricted access to all administrative functions including database modifications.
                    @if(session('admin_password_confirmed') && session('admin_password_confirmed') > now()->subMinutes(30))
                        <br><small><i class="fa fa-check-circle"></i> Password authentication valid until {{ session('admin_password_confirmed')->addMinutes(30)->format('H:i:s') }}</small>
                        <a href="{{ route('admin.clear-auth') }}" class="btn btn-xs btn-default pull-right" onclick="return confirm('Clear authentication session?')">
                            <i class="fa fa-times"></i> Clear Auth
                        </a>
                    @endif
                </div>
                @else
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-warning"></i> Limited Administrative Access</h4>
                    Your account ({{ auth()->user()->email }}) has read-only admin access. 
                    Database modifications are restricted to authorized personnel (daniel@quty.co.id).
                </div>
                @endif
            </div>
        </div>

        <div class="row">
            <!-- System Status -->
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-cog"></i> System Status
                        </h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-condensed">
                            <tr>
                                <td><strong>Database:</strong></td>
                                <td>
                                    <span class="label label-success">
                                        <i class="fa fa-check"></i> Connected
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Cache:</strong></td>
                                <td>
                                    <span class="label label-{{ $system_status['cache'] ? 'success' : 'warning' }}">
                                        <i class="fa fa-{{ $system_status['cache'] ? 'check' : 'exclamation-triangle' }}"></i> 
                                        {{ $system_status['cache'] ? 'Working' : 'Issues' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Storage:</strong></td>
                                <td>
                                    <span class="label label-{{ $system_status['storage'] ? 'success' : 'danger' }}">
                                        <i class="fa fa-{{ $system_status['storage'] ? 'check' : 'times' }}"></i> 
                                        {{ $system_status['storage'] ? 'Writable' : 'Read-only' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>PHP Version:</strong></td>
                                <td><span class="label label-info">{{ PHP_VERSION }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Laravel Version:</strong></td>
                                <td><span class="label label-info">{{ app()->version() }}</span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-md-6">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-flash"></i> Quick Actions
                        </h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-6">
                                <a href="{{ route('users.index') }}" class="btn btn-app">
                                    <i class="fa fa-users"></i> Manage Users
                                </a>
                            </div>
                            <div class="col-xs-6">
                                <a href="{{ route('system.settings') }}" class="btn btn-app">
                                    <i class="fa fa-cogs"></i> System Settings
                                </a>
                            </div>
                            <div class="col-xs-6">
                                <a href="{{ route('admin.database.index') }}" class="btn btn-app">
                                    <i class="fa fa-database"></i> Database
                                    @if(auth()->user()->email !== 'daniel@quty.co.id')
                                        <span class="badge bg-orange"><i class="fa fa-eye"></i></span>
                                    @else
                                        <span class="badge bg-green"><i class="fa fa-edit"></i></span>
                                    @endif
                                </a>
                            </div>
                            <div class="col-xs-6">
                                @if(auth()->user()->email === 'daniel@quty.co.id')
                                <a href="{{ route('admin.cache') }}" class="btn btn-app">
                                    <i class="fa fa-refresh"></i> Clear Cache
                                    <span class="badge bg-green"><i class="fa fa-unlock"></i></span>
                                </a>
                                @else
                                <span class="btn btn-app disabled" title="Restricted to daniel@quty.co.id">
                                    <i class="fa fa-refresh"></i> Clear Cache
                                    <span class="badge bg-red"><i class="fa fa-lock"></i></span>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-history"></i> Recent System Activity
                        </h3>
                    </div>
                    <div class="box-body">
                        @if(isset($recent_activities) && count($recent_activities) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recent_activities as $activity)
                                    <tr>
                                        <td>{{ $activity['time'] }}</td>
                                        <td>{{ $activity['user'] }}</td>
                                        <td>
                                            <span class="label label-{{ $activity['type'] }}">
                                                {{ $activity['action'] }}
                                            </span>
                                        </td>
                                        <td>{{ $activity['details'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-muted">No recent activity to display.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
