@extends('layouts.app')

@section('page_title')
    System Settings
@endsection

@section('page_description')
    Manage system configurations and settings
@endsection

@section('main-content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-cogs"></i> System Settings
                </h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <!-- Ticket Configuration Section -->
                    <div class="col-md-6">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="fa fa-ticket"></i> Ticket Configuration
                                </h3>
                            </div>
                            <div class="box-body">
                                <div class="list-group">
                                    <a href="{{ route('system-settings.canned-fields') }}" class="list-group-item">
                                        <i class="fa fa-text-width"></i> Canned Fields
                                        <span class="pull-right">
                                            <i class="fa fa-chevron-right"></i>
                                        </span>
                                    </a>
                                    <a href="{{ route('system-settings.ticket-statuses') }}" class="list-group-item">
                                        <i class="fa fa-flag"></i> Ticket Statuses
                                        <span class="pull-right">
                                            <i class="fa fa-chevron-right"></i>
                                        </span>
                                    </a>
                                    <a href="{{ route('system-settings.ticket-types') }}" class="list-group-item">
                                        <i class="fa fa-tags"></i> Ticket Types
                                        <span class="pull-right">
                                            <i class="fa fa-chevron-right"></i>
                                        </span>
                                    </a>
                                    <a href="{{ route('system-settings.ticket-priorities') }}" class="list-group-item">
                                        <i class="fa fa-exclamation-triangle"></i> Ticket Priorities
                                        <span class="pull-right">
                                            <i class="fa fa-chevron-right"></i>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Asset Configuration Section -->
                    <div class="col-md-6">
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="fa fa-desktop"></i> Asset Configuration
                                </h3>
                            </div>
                            <div class="box-body">
                                <div class="list-group">
                                    <a href="{{ route('system-settings.asset-statuses') }}" class="list-group-item">
                                        <i class="fa fa-info-circle"></i> Asset Statuses
                                        <span class="pull-right">
                                            <i class="fa fa-chevron-right"></i>
                                        </span>
                                    </a>
                                    <a href="{{ route('system-settings.divisions') }}" class="list-group-item">
                                        <i class="fa fa-building"></i> Divisions
                                        <span class="pull-right">
                                            <i class="fa fa-chevron-right"></i>
                                        </span>
                                    </a>
                                    <a href="{{ route('system-settings.suppliers') }}" class="list-group-item">
                                        <i class="fa fa-truck"></i> Suppliers
                                        <span class="pull-right">
                                            <i class="fa fa-chevron-right"></i>
                                        </span>
                                    </a>
                                    <a href="{{ route('system-settings.invoices') }}" class="list-group-item">
                                        <i class="fa fa-file-text"></i> Invoices
                                        <span class="pull-right">
                                            <i class="fa fa-chevron-right"></i>
                                        </span>
                                    </a>
                                    <a href="{{ route('system-settings.warranty-types') }}" class="list-group-item">
                                        <i class="fa fa-shield"></i> Warranty Types
                                        <span class="pull-right">
                                            <i class="fa fa-chevron-right"></i>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Storeroom Configuration Section -->
                    <div class="col-md-6">
                        <div class="box box-warning">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="fa fa-archive"></i> Storeroom Management
                                </h3>
                            </div>
                            <div class="box-body">
                                <div class="list-group">
                                    <a href="{{ route('system-settings.storeroom') }}" class="list-group-item">
                                        <i class="fa fa-cube"></i> Storeroom Items
                                        <span class="pull-right">
                                            <i class="fa fa-chevron-right"></i>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Information Section -->
                    <div class="col-md-6">
                        <div class="box box-default">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="fa fa-info"></i> System Information
                                </h3>
                            </div>
                            <div class="box-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <td><strong>Application Version:</strong></td>
                                        <td>{{ config('app.version', '1.0.0') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Laravel Version:</strong></td>
                                        <td>{{ app()->version() }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>PHP Version:</strong></td>
                                        <td>{{ PHP_VERSION }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Environment:</strong></td>
                                        <td>
                                            <span class="label label-{{ app()->environment() === 'production' ? 'success' : 'warning' }}">
                                                {{ strtoupper(app()->environment()) }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Add hover effect to list group items
    $('.list-group-item').hover(
        function() {
            $(this).addClass('list-group-item-info');
        },
        function() {
            $(this).removeClass('list-group-item-info');
        }
    );
});
</script>
@endsection