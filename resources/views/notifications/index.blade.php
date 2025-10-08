@extends('layouts.app')

@section('content-header')
  <h1>
    Notifications
    <small>Your system notifications</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Notifications</li>
  </ol>
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">
    
    <!-- Notification Summary -->
    <div class="row">
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
          <span class="info-box-icon bg-aqua"><i class="fa fa-bell"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Total</span>
            <span class="info-box-number">{{ $summary['total'] }}</span>
          </div>
        </div>
      </div>
      
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
          <span class="info-box-icon bg-red"><i class="fa fa-bell"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Unread</span>
            <span class="info-box-number">{{ $summary['unread'] }}</span>
          </div>
        </div>
      </div>
      
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
          <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Read</span>
            <span class="info-box-number">{{ $summary['total'] - $summary['unread'] }}</span>
          </div>
        </div>
      </div>
      
      <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
          <span class="info-box-icon bg-yellow"><i class="fa fa-clock-o"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Recent</span>
            <span class="info-box-number">{{ $summary['recent']->count() }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Notifications List -->
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">
          <i class="fa fa-bell"></i> Notifications
        </h3>
        <div class="box-tools pull-right">
          @if($summary['unread'] > 0)
          <button type="button" class="btn btn-sm btn-success" id="mark-all-read">
            <i class="fa fa-check"></i> Mark All Read
          </button>
          @endif
          
          <!-- Filter Dropdown -->
          <div class="btn-group">
            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-filter"></i> Filter <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
              <li><a href="{{ route('notifications.index') }}">All Notifications</a></li>
              <li><a href="{{ route('notifications.index', ['status' => 'unread']) }}">Unread Only</a></li>
              <li><a href="{{ route('notifications.index', ['status' => 'read']) }}">Read Only</a></li>
              <li class="divider"></li>
              <li><a href="{{ route('notifications.index', ['type' => 'ticket_overdue']) }}">Overdue Tickets</a></li>
              <li><a href="{{ route('notifications.index', ['type' => 'warranty_expiring']) }}">Warranty Expiring</a></li>
              <li><a href="{{ route('notifications.index', ['type' => 'asset_assigned']) }}">Asset Assignments</a></li>
            </ul>
          </div>
        </div>
      </div>
      
      <div class="box-body">
        @if($notifications->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th width="50">Status</th>
                  <th width="100">Type</th>
                  <th width="100">Priority</th>
                  <th>Title</th>
                  <th>Message</th>
                  <th width="120">Time</th>
                  <th width="100">Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($notifications as $notification)
                <tr class="{{ $notification->is_read ? '' : 'alert-info' }}">
                  <td>
                    @if($notification->is_read)
                      <span class="label label-success"><i class="fa fa-check"></i></span>
                    @else
                      <span class="label label-warning"><i class="fa fa-bell"></i></span>
                    @endif
                  </td>
                  <td>
                    <i class="{{ $notification->icon_class }}"></i>
                    <small>{{ ucfirst(str_replace('_', ' ', $notification->type)) }}</small>
                  </td>
                  <td>
                    {!! $notification->priority_badge !!}
                  </td>
                  <td>
                    <strong>{{ $notification->title }}</strong>
                  </td>
                  <td>
                    {{ Str::limit($notification->message, 100) }}
                  </td>
                  <td>
                    <small class="text-muted">{{ $notification->time_ago }}</small>
                  </td>
                  <td>
                    <div class="btn-group">
                      @if($notification->action_url)
                        <a href="{{ $notification->action_url }}" class="btn btn-xs btn-primary" title="View Details">
                          <i class="fa fa-eye"></i>
                        </a>
                      @endif
                      
                      @if(!$notification->is_read)
                        <button type="button" class="btn btn-xs btn-success mark-read" data-id="{{ $notification->id }}" title="Mark as Read">
                          <i class="fa fa-check"></i>
                        </button>
                      @else
                        <button type="button" class="btn btn-xs btn-warning mark-unread" data-id="{{ $notification->id }}" title="Mark as Unread">
                          <i class="fa fa-undo"></i>
                        </button>
                      @endif
                      
                      <button type="button" class="btn btn-xs btn-danger delete-notification" data-id="{{ $notification->id }}" title="Delete">
                        <i class="fa fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          
          <!-- Pagination -->
          <div class="text-center">
            {{ $notifications->appends(request()->query())->links() }}
          </div>
          
        @else
          <div class="text-center">
            <div class="empty-state">
              <i class="fa fa-bell-slash fa-3x text-muted"></i>
              <h3>No notifications found</h3>
              <p class="text-muted">You're all caught up! No notifications to display.</p>
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Notification Type Breakdown -->
@if(count($summary['by_type']) > 0)
<div class="row">
  <div class="col-md-6">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">Notifications by Type</h3>
      </div>
      <div class="box-body">
        @foreach($summary['by_type'] as $type => $count)
        <div class="progress-group">
          <span class="progress-text">{{ ucfirst(str_replace('_', ' ', $type)) }}</span>
          <span class="float-right"><b>{{ $count }}</b>/{{ $summary['total'] }}</span>
          <div class="progress progress-sm">
            <div class="progress-bar progress-bar-primary" style="width: {{ $summary['total'] > 0 ? ($count / $summary['total']) * 100 : 0 }}%"></div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endif
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Mark single notification as read
    $('.mark-read').click(function() {
        var notificationId = $(this).data('id');
        var button = $(this);
        var row = button.closest('tr');
        
        $.post('{{ url("/notifications") }}/' + notificationId + '/read', {
            _token: '{{ csrf_token() }}'
        }).done(function(response) {
            if (response.success) {
                // Update UI
                row.removeClass('alert-info');
                button.removeClass('btn-success mark-read')
                      .addClass('btn-warning mark-unread')
                      .attr('title', 'Mark as Unread')
                      .html('<i class="fa fa-undo"></i>');
                
                // Update status column
                row.find('td:first .label')
                   .removeClass('label-warning')
                   .addClass('label-success')
                   .html('<i class="fa fa-check"></i>');
                
                toastr.success(response.message);
                location.reload(); // Refresh to update counts
            }
        }).fail(function() {
            toastr.error('Failed to mark notification as read');
        });
    });
    
    // Mark single notification as unread
    $(document).on('click', '.mark-unread', function() {
        var notificationId = $(this).data('id');
        var button = $(this);
        var row = button.closest('tr');
        
        $.post('{{ url("/notifications") }}/' + notificationId + '/unread', {
            _token: '{{ csrf_token() }}'
        }).done(function(response) {
            if (response.success) {
                // Update UI
                row.addClass('alert-info');
                button.removeClass('btn-warning mark-unread')
                      .addClass('btn-success mark-read')
                      .attr('title', 'Mark as Read')
                      .html('<i class="fa fa-check"></i>');
                
                // Update status column
                row.find('td:first .label')
                   .removeClass('label-success')
                   .addClass('label-warning')
                   .html('<i class="fa fa-bell"></i>');
                
                toastr.success(response.message);
                location.reload(); // Refresh to update counts
            }
        }).fail(function() {
            toastr.error('Failed to mark notification as unread');
        });
    });
    
    // Mark all notifications as read
    $('#mark-all-read').click(function() {
        if (confirm('Are you sure you want to mark all notifications as read?')) {
            $.post('{{ route("notifications.mark-all-read") }}', {
                _token: '{{ csrf_token() }}'
            }).done(function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload();
                }
            }).fail(function() {
                toastr.error('Failed to mark all notifications as read');
            });
        }
    });
    
    // Delete notification
    $('.delete-notification').click(function() {
        var notificationId = $(this).data('id');
        var row = $(this).closest('tr');
        
        if (confirm('Are you sure you want to delete this notification?')) {
            $.ajax({
                url: '{{ url("/notifications") }}/' + notificationId,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                }
            }).done(function(response) {
                if (response.success) {
                    row.fadeOut();
                    toastr.success(response.message);
                    location.reload(); // Refresh to update counts
                }
            }).fail(function() {
                toastr.error('Failed to delete notification');
            });
        }
    });
});
</script>
@endsection

@section('style')
<style>
.empty-state {
    padding: 60px 20px;
}

.alert-info {
    background-color: #f9f9f9 !important;
}

.progress-group {
    margin-bottom: 15px;
}

.progress-group .progress-text {
    font-weight: 600;
}

.info-box-number {
    font-size: 30px;
    font-weight: bold;
}
</style>
@endsection