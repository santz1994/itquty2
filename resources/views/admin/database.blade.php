@extends('layouts.app')

@section('main-content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Database Administration
            <small>Database management and maintenance</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('/home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="{{ route('admin.dashboard') }}">Admin</a></li>
            <li class="active">Database</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <!-- Database Info -->
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-database"></i> Database Information
                        </h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-condensed">
                            <tr>
                                <td><strong>Connection:</strong></td>
                                <td>
                                    <span class="label label-{{ $db_status['connected'] ? 'success' : 'danger' }}">
                                        {{ $db_status['connected'] ? 'Connected' : 'Disconnected' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Driver:</strong></td>
                                <td>{{ $db_info['driver'] ?? 'Unknown' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Database:</strong></td>
                                <td>{{ $db_info['database'] ?? 'Unknown' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Host:</strong></td>
                                <td>{{ $db_info['host'] ?? 'Unknown' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Port:</strong></td>
                                <td>{{ $db_info['port'] ?? 'Unknown' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Tables:</strong></td>
                                <td>{{ $db_stats['total_tables'] ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td><strong>Database Size:</strong></td>
                                <td>{{ $db_stats['database_size'] ?? 'Unknown' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Database Actions -->
            <div class="col-md-6">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-wrench"></i> Database Actions
                        </h3>
                    </div>
                    <div class="box-body">
                        <form method="POST" action="{{ route('admin.database.action') }}" id="db-action-form">
                            @csrf
                            <div class="form-group">
                                <label for="action">Select Action:</label>
                                <select class="form-control" id="action" name="action" required>
                                    <option value="">Choose an action...</option>
                                    <option value="optimize">Optimize Tables</option>
                                    <option value="repair">Repair Tables</option>
                                    <option value="check">Check Tables</option>
                                    <option value="migrate">Run Migrations</option>
                                    <option value="seed">Run Seeders</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-warning btn-block" onclick="return confirmAction()">
                                <i class="fa fa-play"></i> Execute Action
                            </button>
                        </form>
                    </div>
                </div>

                <div class="box box-danger">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-exclamation-triangle"></i> Danger Zone
                        </h3>
                    </div>
                    <div class="box-body">
                        <p class="text-red"><strong>Warning:</strong> These actions can cause data loss!</p>
                        <form method="POST" action="{{ route('admin.database.danger') }}" id="danger-form">
                            @csrf
                            <div class="form-group">
                                <select class="form-control" name="danger_action" required>
                                    <option value="">Choose dangerous action...</option>
                                    <option value="reset">Reset Database</option>
                                    <option value="fresh">Fresh Migration</option>
                                    <option value="rollback">Rollback Migration</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-danger btn-block" onclick="return confirmDangerousAction()">
                                <i class="fa fa-warning"></i> Execute (DANGEROUS)
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tables List -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-table"></i> Database Tables
                        </h3>
                    </div>
                    <div class="box-body">
                        @if(isset($tables) && count($tables) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Table Name</th>
                                        <th>Rows</th>
                                        <th>Size</th>
                                        <th>Engine</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tables as $table)
                                    <tr>
                                        <td><strong>{{ $table['name'] }}</strong></td>
                                        <td>{{ number_format($table['rows'] ?? 0) }}</td>
                                        <td>{{ $table['size'] ?? 'Unknown' }}</td>
                                        <td>{{ $table['engine'] ?? 'Unknown' }}</td>
                                        <td>{{ $table['created'] ?? 'Unknown' }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-xs btn-info" onclick="viewTable('{{ $table['name'] }}')">
                                                    <i class="fa fa-eye"></i> View
                                                </button>
                                                <button class="btn btn-xs btn-warning" onclick="optimizeTable('{{ $table['name'] }}')">
                                                    <i class="fa fa-wrench"></i> Optimize
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-muted">No tables found or unable to retrieve table information.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Migration Status -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-code-fork"></i> Migration Status
                        </h3>
                    </div>
                    <div class="box-body">
                        @if(isset($migrations) && count($migrations) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Migration</th>
                                        <th>Batch</th>
                                        <th>Executed At</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($migrations as $migration)
                                    <tr>
                                        <td>{{ $migration['name'] }}</td>
                                        <td>{{ $migration['batch'] }}</td>
                                        <td>{{ $migration['executed_at'] }}</td>
                                        <td>
                                            <span class="label label-success">
                                                <i class="fa fa-check"></i> Completed
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-muted">No migration records found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function confirmAction() {
    var action = document.getElementById('action').value;
    if (!action) {
        alert('Please select an action first.');
        return false;
    }
    return confirm('Are you sure you want to execute: ' + action + '?');
}

function confirmDangerousAction() {
    var action = document.querySelector('select[name="danger_action"]').value;
    if (!action) {
        alert('Please select a dangerous action first.');
        return false;
    }
    return confirm('WARNING: This action (' + action + ') can cause permanent data loss! Are you absolutely sure you want to continue?');
}

function viewTable(tableName) {
    // Implement table viewing functionality
    alert('View table: ' + tableName + ' (Feature to be implemented)');
}

function optimizeTable(tableName) {
    if (confirm('Optimize table: ' + tableName + '?')) {
        // Implement table optimization
        alert('Optimize table: ' + tableName + ' (Feature to be implemented)');
    }
}
</script>
@endsection