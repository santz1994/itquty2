@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            System Settings
            <small>Configure system parameters and view information</small>
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <!-- System Information -->
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-info-circle"></i> System Information
                        </h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>Application Version</strong></td>
                                <td>{{ $systemInfo['app_version'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Laravel Version</strong></td>
                                <td>{{ $systemInfo['laravel_version'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>PHP Version</strong></td>
                                <td>{{ $systemInfo['php_version'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Database</strong></td>
                                <td>{{ $systemInfo['database_connection'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Cache Driver</strong></td>
                                <td>{{ $systemInfo['cache_driver'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Session Driver</strong></td>
                                <td>{{ $systemInfo['session_driver'] }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-md-6">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-cogs"></i> Quick Actions
                        </h3>
                    </div>
                    <div class="box-body">
                        <div class="btn-group-vertical" role="group" style="width: 100%;">
                            <a href="{{ route('system.permissions') }}" class="btn btn-primary">
                                <i class="fa fa-key"></i> Manage Permissions
                            </a>
                            <a href="{{ route('system.roles') }}" class="btn btn-info">
                                <i class="fa fa-users"></i> Manage Roles
                            </a>
                            <a href="{{ route('system.maintenance') }}" class="btn btn-warning">
                                <i class="fa fa-wrench"></i> System Maintenance
                            </a>
                            <a href="{{ route('system.logs') }}" class="btn btn-default">
                                <i class="fa fa-file-text"></i> View System Logs
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-heartbeat"></i> System Status
                        </h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-green">
                                        <i class="fa fa-database"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Database</span>
                                        <span class="info-box-number">Online</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-blue">
                                        <i class="fa fa-tachometer"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Cache</span>
                                        <span class="info-box-number">{{ ucfirst($systemInfo['cache_driver']) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-yellow">
                                        <i class="fa fa-list"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Queue</span>
                                        <span class="info-box-number">{{ ucfirst($systemInfo['queue_driver']) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-red">
                                        <i class="fa fa-key"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Session</span>
                                        <span class="info-box-number">{{ ucfirst($systemInfo['session_driver']) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection