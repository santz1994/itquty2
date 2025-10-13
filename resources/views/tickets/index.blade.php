@extends('layouts.app')

@section('main-content')
  <div class="row">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">{{$pageTitle}}</h3>
        </div>
        <div class="box-body">
          <p><a href="tickets/create"><button type="button" class="btn btn-default" name="create-new-ticket" data-toggle="tooltip" data-original-title="Create New Ticket"><span class='fa fa-plus' aria-hidden='true'></span> <b>Create New Ticket</b></button></a></p>
          
          <!-- Filters -->
          <form method="GET" class="form-inline" style="margin-bottom: 20px;">
            <div class="form-group">
              <label for="status">Status:</label>
              <select name="status" id="status" class="form-control" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                @foreach($statuses as $status)
                  <option value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>
                    {{ $status->status }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="form-group" style="margin-left: 10px;">
              <label for="priority">Priority:</label>
              <select name="priority" id="priority" class="form-control" onchange="this.form.submit()">
                <option value="">All Priorities</option>
                @foreach($priorities as $priority)
                  <option value="{{ $priority->id }}" {{ request('priority') == $priority->id ? 'selected' : '' }}>
                    {{ $priority->priority }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="form-group" style="margin-left: 10px;">
              <label for="asset_id">Asset:</label>
              <select name="asset_id" id="asset_id" class="form-control" onchange="this.form.submit()">
                <option value="">All Assets</option>
                @foreach($assets as $asset)
                  <option value="{{ $asset->id }}" {{ request('asset_id') == $asset->id ? 'selected' : '' }}>
                    {{ $asset->model_name ? $asset->model_name : 'Unknown Model' }} ({{ $asset->asset_tag }})
                  </option>
                @endforeach
              </select>
            </div>
            @if(!auth()->user()->hasRole('user'))
            <div class="form-group" style="margin-left: 10px;">
              <label for="assigned_to">Assigned To:</label>
              <select name="assigned_to" id="assigned_to" class="form-control" onchange="this.form.submit()">
                <option value="">All Admins</option>
                @foreach($admins as $admin)
                  <option value="{{ $admin->id }}" {{ request('assigned_to') == $admin->id ? 'selected' : '' }}>
                    {{ $admin->name }}
                  </option>
                @endforeach
              </select>
            </div>
            @endif
            <div class="form-group" style="margin-left: 10px;">
              <input type="text" name="search" placeholder="Search tickets..." class="form-control" value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-primary" style="margin-left: 10px;">Filter</button>
            <a href="{{ route('tickets.index') }}" class="btn btn-default" style="margin-left: 5px;">Clear</a>
          </form>
          
          <table id="table" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th>Ticket Number</th>
                <th>Creator Ticket</th>
                <th>Location</th>
                <th>Asset</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Subject</th>
                @if(!auth()->user()->hasRole('user'))
                  <th>Assigned To</th>
                @endif
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($tickets as $ticket)
                <tr>
                  <div>
                    <td>{{$ticket->ticket_code}}</td>
                    <td><div class="hover-pointer" id="agent{{$ticket->id}}">{{$ticket->user->name}}</div></td>
                    <td><div class="hover-pointer" id="location{{$ticket->id}}">{{$ticket->location->location_name}}</div></td>
                    <td><div class="hover-pointer" id="asset{{$ticket->id}}">
                      @if($ticket->asset)
                        {{ $ticket->asset->name }} ({{ $ticket->asset->asset_tag }})
                      @else
                        <span class="text-muted">No Asset</span>
                      @endif
                    </div></td>
                    <td>
                      <div class="hover-pointer" id="status{{$ticket->id}}">
                        @if($ticket->ticket_status->status == 'Open')
                          <span class="label label-success">
                        @elseif($ticket->ticket_status->status == 'Pending')
                          <span class="label label-info">
                        @elseif($ticket->ticket_status->status == 'Resolved')
                          <span class="label label-warning">
                        @elseif($ticket->ticket_status->status == 'Closed')
                          <span class="label label-danger">
                        @endif
                        {{$ticket->ticket_status->status}}</span>
                      </div>
                    </td>
                    <td>
                      <div class="hover-pointer" id="priority{{$ticket->id}}">
                        @if($ticket->ticket_priority->priority == 'Low')
                          <span class="label label-success">
                        @elseif($ticket->ticket_priority->priority == 'Medium')
                          <span class="label label-warning">
                        @elseif($ticket->ticket_priority->priority == 'High')
                          <span class="label label-danger">
                        @endif
                        {{$ticket->ticket_priority->priority}}</span>
                      </div>
                    </td>
                      <td>{{$ticket->subject}}</td>
                      @if(!auth()->user()->hasRole('user'))
                        <td>
                          <div class="hover-pointer" id="assigned{{$ticket->id}}">
                            @if($ticket->assignedTo)
                              {{ $ticket->assignedTo->name }}
                            @else
                              <span class="text-muted">Unassigned</span>
                            @endif
                          </div>
                        </td>
                      @endif
                      <td><a href="/tickets/{{ $ticket->id }}" class="btn btn-primary"><span class="fa fa-ticket" aria-hidden="true"></span> <b>View</b></a></td>
                  </div>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <script>
      $(document).ready(function() {
        var table = $('#table').DataTable( {
          responsive: true,
          columnDefs: [ {
            orderable: false, targets: -1
          } ],
          order: [[ 0, "desc" ]]
        } );
        // Get the agent, locatoin, status and priority columns' div IDs for each row.
        // If it is clicked on, then the datatable will filter that.
        @foreach($tickets as $ticket)
          // Agent
          var agent = (function() {
            var x = '#agent' + {{$ticket->id}};
            return x;
          });
          $(agent()).click(function () {
            table.search( "{{$ticket->user->name}}" ).draw();
          });

          // Location
          var location = (function() {
            var x = '#location' + {{$ticket->id}};
            return x;
          });
          $(location()).click(function () {
            table.search( "{{$ticket->location->location_name}}" ).draw();
          });

          // Asset
          var asset = (function() {
            var x = '#asset' + {{$ticket->id}};
            return x;
          });
          $(asset()).click(function () {
            @if($ticket->asset)
              table.search( "{{ $ticket->asset->asset_tag }}" ).draw();
            @endif
          });

          // Status
          var status = (function() {
            var x = '#status' + {{$ticket->id}};
            return x;
          });
          $(status()).click(function () {
            table.search( "{{$ticket->ticket_status->status}}" ).draw();
          });

          // Priority
          var priority = (function() {
            var x = '#priority' + {{$ticket->id}};
            return x;
          });
          $(priority()).click(function () {
            table.search( "{{$ticket->ticket_priority->priority}}" ).draw();
          });
            @if(!auth()->user()->hasRole('user'))
            // Assigned To
            var assigned = (function() {
              var x = '#assigned' + {{$ticket->id}};
              return x;
            });
            $(assigned()).click(function () {
              @if($ticket->assignedTo)
                table.search( "{{ $ticket->assignedTo->name }}" ).draw();
              @endif
            });
            @endif
        @endforeach
      } );

    </script>
@endsection


