@extends('layouts.app')

@section('main-content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            System Logs
            <small>View and manage application logs</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('/home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="{{ route('system.settings') }}">System</a></li>
            <li class="active">Logs</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <!-- Log Viewer -->
            <div class="col-md-9">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-file-text"></i> Log Entries
                        </h3>
                        <div class="box-tools pull-right">
                            <form method="GET" action="{{ route('system.logs') }}" style="display: inline;">
                                <div class="input-group input-group-sm" style="width: 200px;">
                                    <input type="text" name="search" class="form-control" placeholder="Search logs..." value="{{ request('search') }}">
                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="box-body" style="max-height: 600px; overflow-y: auto;">
                        @if(isset($logs) && count($logs) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-condensed">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Level</th>
                                        <th>Message</th>
                                        <th>Context</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($logs as $log)
                                    <tr class="{{ $log['level'] === 'error' ? 'danger' : ($log['level'] === 'warning' ? 'warning' : '') }}">
                                        <td>{{ $log['timestamp'] ?? 'Unknown' }}</td>
                                        <td>
                                            <span class="label label-{{ $log['level'] === 'error' ? 'danger' : ($log['level'] === 'warning' ? 'warning' : ($log['level'] === 'info' ? 'info' : 'default')) }}">
                                                {{ strtoupper($log['level'] ?? 'UNKNOWN') }}
                                            </span>
                                        </td>
                                        <td>{{ $log['message'] ?? 'No message' }}</td>
                                        <td>
                                            @if(isset($log['context']) && !empty($log['context']))
                                            <button class="btn btn-xs btn-info" onclick="showContext('{{ $log['id'] ?? 'unknown' }}')">
                                                <i class="fa fa-info-circle"></i> View
                                            </button>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center">
                            <i class="fa fa-file-text-o fa-3x text-muted"></i>
                            <p class="text-muted">No log entries found.</p>
                            @if(request('search'))
                            <p>Try a different search term or <a href="{{ route('system.logs') }}">view all logs</a>.</p>
                            @endif
                        </div>
                        @endif
                    </div>
                    @if(isset($logs) && count($logs) > 0)
                    <div class="box-footer">
                        <small class="text-muted">Showing {{ count($logs) }} log entries</small>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Log Controls -->
            <div class="col-md-3">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-cogs"></i> Log Controls
                        </h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label>Filter by Level:</label>
                            <form method="GET" action="{{ route('system.logs') }}">
                                <select class="form-control" name="level" onchange="this.form.submit()">
                                    <option value="">All Levels</option>
                                    <option value="emergency" {{ request('level') === 'emergency' ? 'selected' : '' }}>Emergency</option>
                                    <option value="alert" {{ request('level') === 'alert' ? 'selected' : '' }}>Alert</option>
                                    <option value="critical" {{ request('level') === 'critical' ? 'selected' : '' }}>Critical</option>
                                    <option value="error" {{ request('level') === 'error' ? 'selected' : '' }}>Error</option>
                                    <option value="warning" {{ request('level') === 'warning' ? 'selected' : '' }}>Warning</option>
                                    <option value="notice" {{ request('level') === 'notice' ? 'selected' : '' }}>Notice</option>
                                    <option value="info" {{ request('level') === 'info' ? 'selected' : '' }}>Info</option>
                                    <option value="debug" {{ request('level') === 'debug' ? 'selected' : '' }}>Debug</option>
                                </select>
                                @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                @endif
                            </form>
                        </div>

                        <div class="form-group">
                            <label>Date Range:</label>
                            <form method="GET" action="{{ route('system.logs') }}">
                                <select class="form-control" name="date" onchange="this.form.submit()">
                                    <option value="">All Dates</option>
                                    <option value="today" {{ request('date') === 'today' ? 'selected' : '' }}>Today</option>
                                    <option value="yesterday" {{ request('date') === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                                    <option value="week" {{ request('date') === 'week' ? 'selected' : '' }}>This Week</option>
                                    <option value="month" {{ request('date') === 'month' ? 'selected' : '' }}>This Month</option>
                                </select>
                                @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                @endif
                                @if(request('level'))
                                <input type="hidden" name="level" value="{{ request('level') }}">
                                @endif
                            </form>
                        </div>

                        <hr>

                        <form method="POST" action="{{ route('system.logs.clear') }}">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Are you sure you want to clear all logs? This action cannot be undone.')">
                                <i class="fa fa-trash"></i> Clear All Logs
                            </button>
                        </form>

                        <a href="{{ route('system.logs.download') }}" class="btn btn-info btn-block">
                            <i class="fa fa-download"></i> Download Logs
                        </a>
                    </div>
                </div>

                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-info-circle"></i> Log Statistics
                        </h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-condensed">
                            <tr>
                                <td><strong>Total Entries:</strong></td>
                                <td>{{ $stats['total'] ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td><strong>Errors:</strong></td>
                                <td><span class="text-red">{{ $stats['errors'] ?? 0 }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Warnings:</strong></td>
                                <td><span class="text-yellow">{{ $stats['warnings'] ?? 0 }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Info:</strong></td>
                                <td><span class="text-blue">{{ $stats['info'] ?? 0 }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Log Size:</strong></td>
                                <td>{{ $stats['file_size'] ?? 'Unknown' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Last Entry:</strong></td>
                                <td>{{ $stats['last_entry'] ?? 'Never' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-file"></i> Log Files
                        </h3>
                    </div>
                    <div class="box-body">
                        @if(isset($log_files) && count($log_files) > 0)
                        <ul class="list-unstyled">
                            @foreach($log_files as $file)
                            <li>
                                <a href="{{ route('system.logs', ['file' => $file['name']]) }}" 
                                   class="text-{{ $file['name'] === request('file', 'laravel.log') ? 'primary' : 'default' }}">
                                    <i class="fa fa-file-text-o"></i> {{ $file['name'] }}
                                </a>
                                <br>
                                <small class="text-muted">{{ $file['size'] }} - {{ $file['modified'] }}</small>
                            </li>
                            @if(!$loop->last)<hr style="margin: 10px 0;">@endif
                            @endforeach
                        </ul>
                        @else
                        <p class="text-muted">No log files found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function showContext(logId) {
    // Implement context viewing
    alert('Show context for log ID: ' + logId + ' (Feature to be implemented)');
}
</script>
@endsection
