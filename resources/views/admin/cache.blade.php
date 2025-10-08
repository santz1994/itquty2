@extends('layouts.app')

@section('main-content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Cache Management
            <small>Application cache control and optimization</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('/home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="{{ route('admin.dashboard') }}">Admin</a></li>
            <li class="active">Cache</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <!-- Cache Status -->
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-tachometer"></i> Cache Status
                        </h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-condensed">
                            <tr>
                                <td><strong>Cache Driver:</strong></td>
                                <td>{{ $cache_info['driver'] ?? 'file' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Cache Status:</strong></td>
                                <td>
                                    <span class="label label-{{ $cache_status['working'] ? 'success' : 'danger' }}">
                                        {{ $cache_status['working'] ? 'Working' : 'Not Working' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Application Cache:</strong></td>
                                <td>
                                    <span class="label label-{{ $cache_status['application'] ? 'success' : 'warning' }}">
                                        {{ $cache_status['application'] ? 'Cached' : 'Not Cached' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Route Cache:</strong></td>
                                <td>
                                    <span class="label label-{{ $cache_status['routes'] ? 'success' : 'warning' }}">
                                        {{ $cache_status['routes'] ? 'Cached' : 'Not Cached' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Config Cache:</strong></td>
                                <td>
                                    <span class="label label-{{ $cache_status['config'] ? 'success' : 'warning' }}">
                                        {{ $cache_status['config'] ? 'Cached' : 'Not Cached' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>View Cache:</strong></td>
                                <td>
                                    <span class="label label-{{ $cache_status['views'] ? 'success' : 'warning' }}">
                                        {{ $cache_status['views'] ? 'Cached' : 'Not Cached' }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Cache Actions -->
            <div class="col-md-6">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-refresh"></i> Cache Actions
                        </h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-6">
                                <form method="POST" action="{{ route('admin.cache.clear') }}">
                                    @csrf
                                    <input type="hidden" name="cache_type" value="application">
                                    <button type="submit" class="btn btn-warning btn-block">
                                        <i class="fa fa-trash"></i> Clear App Cache
                                    </button>
                                </form>
                            </div>
                            <div class="col-xs-6">
                                <form method="POST" action="{{ route('admin.cache.clear') }}">
                                    @csrf
                                    <input type="hidden" name="cache_type" value="config">
                                    <button type="submit" class="btn btn-info btn-block">
                                        <i class="fa fa-cogs"></i> Clear Config
                                    </button>
                                </form>
                            </div>
                            <div class="col-xs-6">
                                <form method="POST" action="{{ route('admin.cache.clear') }}">
                                    @csrf
                                    <input type="hidden" name="cache_type" value="route">
                                    <button type="submit" class="btn btn-success btn-block">
                                        <i class="fa fa-road"></i> Clear Routes
                                    </button>
                                </form>
                            </div>
                            <div class="col-xs-6">
                                <form method="POST" action="{{ route('admin.cache.clear') }}">
                                    @csrf
                                    <input type="hidden" name="cache_type" value="view">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fa fa-eye"></i> Clear Views
                                    </button>
                                </form>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-xs-6">
                                <form method="POST" action="{{ route('admin.cache.clear') }}">
                                    @csrf
                                    <input type="hidden" name="cache_type" value="all">
                                    <button type="submit" class="btn btn-danger btn-block">
                                        <i class="fa fa-bomb"></i> Clear All Cache
                                    </button>
                                </form>
                            </div>
                            <div class="col-xs-6">
                                <form method="POST" action="{{ route('admin.cache.optimize') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-block">
                                        <i class="fa fa-rocket"></i> Optimize Cache
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cache Statistics -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-bar-chart"></i> Cache Statistics
                        </h3>
                    </div>
                    <div class="box-body">
                        @if(isset($cache_stats) && count($cache_stats) > 0)
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-aqua"><i class="fa fa-files-o"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Cache Files</span>
                                        <span class="info-box-number">{{ $cache_stats['total_files'] ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-green"><i class="fa fa-hdd-o"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Cache Size</span>
                                        <span class="info-box-number">{{ $cache_stats['total_size'] ?? '0 MB' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-yellow"><i class="fa fa-clock-o"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Last Cleared</span>
                                        <span class="info-box-number">{{ $cache_stats['last_cleared'] ?? 'Never' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-red"><i class="fa fa-tachometer"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Hit Rate</span>
                                        <span class="info-box-number">{{ $cache_stats['hit_rate'] ?? '0%' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Cache Files -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-file-text"></i> Cache Files Detail
                        </h3>
                    </div>
                    <div class="box-body">
                        @if(isset($cache_files) && count($cache_files) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>File Type</th>
                                        <th>Location</th>
                                        <th>Size</th>
                                        <th>Last Modified</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cache_files as $file)
                                    <tr>
                                        <td><strong>{{ $file['type'] }}</strong></td>
                                        <td><code>{{ $file['path'] }}</code></td>
                                        <td>{{ $file['size'] }}</td>
                                        <td>{{ $file['modified'] }}</td>
                                        <td>
                                            <span class="label label-{{ $file['exists'] ? 'success' : 'danger' }}">
                                                {{ $file['exists'] ? 'Exists' : 'Missing' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($file['exists'])
                                            <form method="POST" action="{{ route('admin.cache.clear') }}" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="cache_type" value="{{ $file['type'] }}">
                                                <button type="submit" class="btn btn-xs btn-warning">
                                                    <i class="fa fa-trash"></i> Clear
                                                </button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-muted">No cache files found or unable to retrieve cache information.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Cache Activity -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-history"></i> Recent Cache Activity
                        </h3>
                    </div>
                    <div class="box-body">
                        @if(isset($recent_cache_activity) && count($recent_cache_activity) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Action</th>
                                        <th>Cache Type</th>
                                        <th>User</th>
                                        <th>Result</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recent_cache_activity as $activity)
                                    <tr>
                                        <td>{{ $activity['time'] }}</td>
                                        <td>
                                            <span class="label label-{{ $activity['action_type'] }}">
                                                {{ $activity['action'] }}
                                            </span>
                                        </td>
                                        <td>{{ $activity['cache_type'] }}</td>
                                        <td>{{ $activity['user'] }}</td>
                                        <td>
                                            <span class="label label-{{ $activity['success'] ? 'success' : 'danger' }}">
                                                {{ $activity['success'] ? 'Success' : 'Failed' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-muted">No recent cache activity to display.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
