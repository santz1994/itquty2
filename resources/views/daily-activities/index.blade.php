@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/custom-tables.css') }}">
@endpush

@section('main-content')
    @include('components.page-header', [
        'title' => 'Daily Activities',
        'subtitle' => 'Track and manage daily work activities',
        'icon' => 'fa-calendar-check-o',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => url('/home'), 'icon' => 'fa-dashboard'],
            ['label' => 'Daily Activities', 'active' => true]
        ],
        'actions' => '<a href="' . route('daily-activities.create') . '" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Add Activity
                      </a>
                      <div class="btn-group">
                          <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                              <i class="fa fa-download"></i> Reports <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu" role="menu">
                              <li><a href="' . route('daily-activities.daily-report') . '?date=' . request('date', today()) . '">
                                  <i class="fa fa-file-text-o"></i> Daily Report
                              </a></li>
                              <li><a href="' . route('daily-activities.weekly-report') . '">
                                  <i class="fa fa-calendar"></i> Weekly Report
                              </a></li>
                              <li><a href="' . route('daily-activities.export-pdf') . '?date=' . request('date', today()) . '">
                                  <i class="fa fa-file-pdf-o"></i> Export PDF
                              </a></li>
                          </ul>
                      </div>'
    ])

    @include('components.loading-overlay')

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-list"></i> Activities List
                        </h3>
                    </div>
            
            <!-- Filters -->
            <div class="box-body">
                <form method="GET" class="form-inline mb-3">
                    <div class="form-group">
                        <label>Date:</label>
                        <input type="date" name="date" class="form-control" 
                               value="{{ request('date', today()->format('Y-m-d')) }}">
                    </div>
                    
                    @role(['super-admin', 'admin'])
                    <div class="form-group">
                        <label>User:</label>
                        <select name="user_id" class="form-control">
                            <option value="">All Users</option>
                            @foreach($users ?? [] as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endrole
                    
                    <button type="submit" class="btn btn-info">
                        <i class="fa fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('daily-activities.index') }}" class="btn btn-default">
                        <i class="fa fa-refresh"></i> Reset
                    </a>
                </form>
                
                <!-- Statistics -->
                @if(isset($stats))
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-blue"><i class="fa fa-tasks"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Activities</span>
                                <span class="info-box-number">{{ $stats['total_activities'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-edit"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Manual Entries</span>
                                <span class="info-box-number">{{ $stats['manual_activities'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-yellow"><i class="fa fa-cogs"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Auto Generated</span>
                                <span class="info-box-number">{{ $stats['auto_activities'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-red"><i class="fa fa-ticket"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Tickets Completed</span>
                                <span class="info-box-number">{{ $stats['tickets_completed'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Activities List -->
                @if($activities->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="120">Date</th>
                                    <th width="150">User</th>
                                    <th>Description</th>
                                    <th width="120">Type</th>
                                    <th width="120">Related Ticket</th>
                                    <th width="100">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activities as $activity)
                                <tr>
                                    <td>
                                        <span class="label label-info">
                                            @if($activity->activity_date)
                                                {{ \Carbon\Carbon::parse($activity->activity_date)->format('d/m/Y') }}
                                            @else
                                                -
                                            @endif
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            @if($activity->created_at)
                                                {{ \Carbon\Carbon::parse($activity->created_at)->format('H:i') }}
                                            @else
                                                -
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <strong>{{ $activity->user->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $activity->user->email }}</small>
                                    </td>
                                    <td>
                                        <div class="activity-description">
                                            {{ \Illuminate\Support\Str::limit($activity->description, 150) }}
                                            @if(strlen($activity->description) > 150)
                                                <a href="#" class="show-full-description" data-description="{{ $activity->description }}">
                                                    <i class="fa fa-expand"></i> Show more
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($activity->type === 'manual')
                                            <span class="label label-primary">
                                                <i class="fa fa-edit"></i> Manual
                                            </span>
                                        @else
                                            <span class="label label-success">
                                                <i class="fa fa-cogs"></i> Auto
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($activity->ticket)
                                            <a href="{{ route('tickets.show', $activity->ticket->id) }}" 
                                               class="btn btn-xs btn-info">
                                                {{ $activity->ticket->ticket_code }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-xs">
                                            <a href="{{ route('daily-activities.show', $activity->id) }}" 
                                               class="btn btn-info" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            @if($activity->type === 'manual' && ($activity->user_id === Auth::id() || Auth::user()->hasRole(['super-admin', 'admin'])))
                                                <a href="{{ route('daily-activities.edit', $activity->id) }}" 
                                                   class="btn btn-warning" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('daily-activities.destroy', $activity->id) }}" 
                                                      method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger" title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this activity?')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="text-center">
                        {{ $activities->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <h4><i class="fa fa-info-circle"></i> No Activities Found</h4>
                        <p>No daily activities found for the selected date and filters.</p>
                        <a href="{{ route('daily-activities.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Add Your First Activity
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Full Description Modal -->
<div class="modal fade" id="descriptionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Activity Description</h4>
            </div>
            <div class="modal-body">
                <p id="fullDescription"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Hide loading overlay when page is fully loaded
    window.addEventListener('load', function() {
        setTimeout(function() {
            hideLoadingOverlay();
        }, 300);
    });
    
    // Show full description modal
    $('.show-full-description').on('click', function(e) {
        e.preventDefault();
        var description = $(this).data('description');
        $('#fullDescription').text(description);
        $('#descriptionModal').modal('show');
    });
    
    // Tooltip initialization
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endpush

