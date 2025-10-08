@extends('layouts.admin')

@section('htmlheader_title')
    Asset Maintenance Logs
@endsection

@section('main-content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Asset Maintenance Logs
            <small>Manage asset maintenance and repair history</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('/home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Maintenance Logs</li>
        </ol>
    </section>

    <section class="content">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-check"></i> Success!</h4>
                {{ session('success') }}
            </div>
        @endif

        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Maintenance Logs</h3>
                <div class="box-tools pull-right">
                    <a href="{{ route('maintenance.create') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus"></i> Add Maintenance Log
                    </a>
                </div>
            </div>

            <div class="box-body">
                <!-- Filters -->
                <div class="row">
                    <div class="col-md-12">
                        <form method="GET" class="form-inline">
                            <div class="form-group">
                                <label>Asset:</label>
                                <select name="asset_id" class="form-control">
                                    <option value="">All Assets</option>
                                    @foreach($assets as $asset)
                                        <option value="{{ $asset->id }}" {{ request('asset_id') == $asset->id ? 'selected' : '' }}>
                                            {{ $asset->asset_tag }} - {{ $asset->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Status:</label>
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Type:</label>
                                <select name="maintenance_type" class="form-control">
                                    <option value="">All Types</option>
                                    @foreach($maintenanceTypes as $type)
                                        <option value="{{ $type }}" {{ request('maintenance_type') == $type ? 'selected' : '' }}>
                                            {{ ucfirst($type) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-default">Filter</button>
                            <a href="{{ route('maintenance.index') }}" class="btn btn-default">Clear</a>
                        </form>
                    </div>
                </div>
                <br>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Asset</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Scheduled</th>
                                <th>Performed By</th>
                                <th>Cost</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($maintenanceLogs as $log)
                                <tr>
                                    <td>
                                        <strong>{{ $log->asset->asset_tag ?? 'N/A' }}</strong><br>
                                        <small>{{ $log->asset->name ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="label label-info">{{ ucfirst($log->maintenance_type) }}</span>
                                    </td>
                                    <td>{{ \Str::limit($log->description, 50) }}</td>
                                    <td>
                                        @php
                                            $statusClass = [
                                                'planned' => 'label-default',
                                                'in_progress' => 'label-warning', 
                                                'completed' => 'label-success',
                                                'cancelled' => 'label-danger'
                                            ];
                                        @endphp
                                        <span class="label {{ $statusClass[$log->status] ?? 'label-default' }}">
                                            {{ ucfirst(str_replace('_', ' ', $log->status)) }}
                                        </span>
                                    </td>
                                    <td>{{ $log->scheduled_at ? $log->scheduled_at->format('M d, Y') : '-' }}</td>
                                    <td>{{ $log->performedBy->name ?? 'N/A' }}</td>
                                    <td>{{ $log->cost ? 'Rp ' . number_format($log->cost, 0, ',', '.') : '-' }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('maintenance.show', $log->id) }}" class="btn btn-xs btn-info">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('maintenance.edit', $log->id) }}" class="btn btn-xs btn-warning">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('maintenance.destroy', $log->id) }}" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-danger" 
                                                        onclick="return confirm('Are you sure?')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No maintenance logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($maintenanceLogs->hasPages())
                    <div class="text-center">
                        {{ $maintenanceLogs->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection