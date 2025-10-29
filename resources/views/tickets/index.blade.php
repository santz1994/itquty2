@extends('layouts.app')

@section('main-content')

@include('components.page-header', [
    'title' => 'Tickets',
    'subtitle' => 'Manage and track all support tickets',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('admin.dashboard'), 'icon' => 'home'],
        ['label' => 'Tickets']
    ],
    'actions' => '<a href="'.route('tickets.create').'" class="btn btn-primary">
        <i class="fa fa-plus"></i> Create New Ticket
    </a>
    <a href="'.route('tickets.export').'" class="btn btn-success">
        <i class="fa fa-download"></i> Export
    </a>'
])

  <div class="row">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-body">

          <!-- Bulk Operations Toolbar -->
          <div id="bulk-actions-toolbar" class="alert alert-info" style="display: none; margin-bottom: 20px;">
            <div class="row">
              <div class="col-md-12">
                <strong><span id="selected-count">0</span> ticket(s) selected</strong>
                <div class="btn-group" style="margin-left: 20px;">
                  <button type="button" class="btn btn-sm btn-primary" onclick="showBulkAssignModal()">
                    <i class="fa fa-user"></i> Assign
                  </button>
                  <button type="button" class="btn btn-sm btn-info" onclick="showBulkStatusModal()">
                    <i class="fa fa-flag"></i> Change Status
                  </button>
                  <button type="button" class="btn btn-sm btn-warning" onclick="showBulkPriorityModal()">
                    <i class="fa fa-exclamation-circle"></i> Change Priority
                  </button>
                  <button type="button" class="btn btn-sm btn-success" onclick="showBulkCategoryModal()">
                    <i class="fa fa-tags"></i> Change Category
                  </button>
                  @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
                  <button type="button" class="btn btn-sm btn-danger" onclick="confirmBulkDelete()">
                    <i class="fa fa-trash"></i> Delete
                  </button>
                  @endif
                </div>
                <button type="button" class="btn btn-sm btn-default pull-right" onclick="clearSelection()">
                  <i class="fa fa-times"></i> Clear Selection
                </button>
              </div>
            </div>
          </div>
          
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
          
          <table id="table" class="table table-enhanced table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th width="30">
                  <input type="checkbox" id="select-all-tickets" onclick="toggleSelectAll(this)">
                </th>
                <th class="sortable" data-column="ticket_number">Ticket Number</th>
                <th class="sortable" data-column="creator">Creator Ticket</th>
                <th class="sortable" data-column="location">Location</th>
                <th class="sortable" data-column="asset">Asset</th>
                <th class="sortable" data-column="status">Status</th>
                <th class="sortable" data-column="priority">Priority</th>
                <th class="sortable" data-column="subject">Subject</th>
                @if(!auth()->user()->hasRole('user'))
                  <th class="sortable" data-column="assigned_to">Assigned To</th>
                @endif
                <th class="actions">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($tickets as $ticket)
                <tr>
                  <div>
                    <td>
                      <input type="checkbox" class="ticket-checkbox" value="{{$ticket->id}}" onchange="updateBulkToolbar()">
                    </td>
                    <td>{{$ticket->ticket_code}}</td>
                    <td><div class="hover-pointer" id="agent{{$ticket->id}}">{{$ticket->user->name}}</div></td>
                    <td><div class="hover-pointer" id="location{{$ticket->id}}">{{$ticket->location->location_name}}</div></td>
                    <td><div class="hover-pointer" id="asset{{$ticket->id}}">
                      @if($ticket->assets && $ticket->assets->count())
                        @foreach($ticket->assets as $a)
                          @if(!$loop->first), @endif{{ $a->name }} ({{ $a->asset_tag }})
                        @endforeach
                      @elseif($ticket->asset)
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

    <!-- Bulk Assign Modal -->
    <div class="modal fade" id="bulkAssignModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><i class="fa fa-user"></i> Bulk Assign Tickets</h4>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="bulk-assign-user">Assign To:</label>
              <select id="bulk-assign-user" class="form-control">
                <option value="">Select User...</option>
              </select>
            </div>
            <p class="text-muted">
              <small><span id="bulk-assign-count">0</span> ticket(s) will be assigned</small>
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="executeBulkAssign()">
              <i class="fa fa-check"></i> Assign
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Bulk Status Modal -->
    <div class="modal fade" id="bulkStatusModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><i class="fa fa-flag"></i> Bulk Update Status</h4>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="bulk-status">Change Status To:</label>
              <select id="bulk-status" class="form-control">
                <option value="">Select Status...</option>
              </select>
            </div>
            <p class="text-muted">
              <small><span id="bulk-status-count">0</span> ticket(s) will be updated</small>
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-info" onclick="executeBulkUpdateStatus()">
              <i class="fa fa-check"></i> Update Status
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Bulk Priority Modal -->
    <div class="modal fade" id="bulkPriorityModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><i class="fa fa-exclamation-circle"></i> Bulk Update Priority</h4>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="bulk-priority">Change Priority To:</label>
              <select id="bulk-priority" class="form-control">
                <option value="">Select Priority...</option>
              </select>
            </div>
            <p class="text-muted">
              <small><span id="bulk-priority-count">0</span> ticket(s) will be updated</small>
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-warning" onclick="executeBulkUpdatePriority()">
              <i class="fa fa-check"></i> Update Priority
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Bulk Category Modal -->
    <div class="modal fade" id="bulkCategoryModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><i class="fa fa-tags"></i> Bulk Update Category</h4>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="bulk-category">Change Category To:</label>
              <select id="bulk-category" class="form-control">
                <option value="">Select Category...</option>
              </select>
            </div>
            <p class="text-muted">
              <small><span id="bulk-category-count">0</span> ticket(s) will be updated</small>
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-success" onclick="executeBulkUpdateCategory()">
              <i class="fa fa-check"></i> Update Category
            </button>
          </div>
        </div>
      </div>
    </div>

    <script>
      // Global variables
      var bulkOptions = {
        users: [],
        statuses: [],
        priorities: [],
        types: []
      };

      // Load bulk options on page load
      $(document).ready(function() {
        loadBulkOptions();
      });

      // Load options for dropdowns
      function loadBulkOptions() {
        $.ajax({
          url: '{{ route("tickets.bulk.options") }}',
          type: 'GET',
          success: function(response) {
            if (response.success) {
              bulkOptions = response.data;
              populateDropdowns();
            }
          },
          error: function(xhr) {
            console.error('Failed to load bulk options:', xhr);
          }
        });
      }

      // Populate all dropdowns
      function populateDropdowns() {
        // Users dropdown
        var usersSelect = $('#bulk-assign-user');
        usersSelect.empty().append('<option value="">Select User...</option>');
        bulkOptions.users.forEach(function(user) {
          usersSelect.append('<option value="' + user.id + '">' + user.name + ' (' + user.email + ')</option>');
        });

        // Statuses dropdown
        var statusesSelect = $('#bulk-status');
        statusesSelect.empty().append('<option value="">Select Status...</option>');
        bulkOptions.statuses.forEach(function(status) {
          statusesSelect.append('<option value="' + status.id + '">' + status.name + '</option>');
        });

        // Priorities dropdown
        var prioritiesSelect = $('#bulk-priority');
        prioritiesSelect.empty().append('<option value="">Select Priority...</option>');
        bulkOptions.priorities.forEach(function(priority) {
          prioritiesSelect.append('<option value="' + priority.id + '">' + priority.name + '</option>');
        });

        // Categories dropdown
        var typesSelect = $('#bulk-category');
        typesSelect.empty().append('<option value="">Select Category...</option>');
        bulkOptions.types.forEach(function(type) {
          typesSelect.append('<option value="' + type.id + '">' + type.name + '</option>');
        });
      }

      // Toggle select all
      function toggleSelectAll(checkbox) {
        $('.ticket-checkbox').prop('checked', checkbox.checked);
        updateBulkToolbar();
      }

      // Update bulk actions toolbar visibility
      function updateBulkToolbar() {
        var selectedCount = $('.ticket-checkbox:checked').length;
        $('#selected-count').text(selectedCount);
        
        if (selectedCount > 0) {
          $('#bulk-actions-toolbar').slideDown();
        } else {
          $('#bulk-actions-toolbar').slideUp();
        }

        // Update select all checkbox state
        var totalCheckboxes = $('.ticket-checkbox').length;
        $('#select-all-tickets').prop('checked', selectedCount === totalCheckboxes);
      }

      // Clear selection
      function clearSelection() {
        $('.ticket-checkbox').prop('checked', false);
        $('#select-all-tickets').prop('checked', false);
        updateBulkToolbar();
      }

      // Get selected ticket IDs
      function getSelectedTicketIds() {
        var ticketIds = [];
        $('.ticket-checkbox:checked').each(function() {
          ticketIds.push($(this).val());
        });
        return ticketIds;
      }

      // Show modals
      function showBulkAssignModal() {
        var selectedCount = getSelectedTicketIds().length;
        $('#bulk-assign-count').text(selectedCount);
        $('#bulkAssignModal').modal('show');
      }

      function showBulkStatusModal() {
        var selectedCount = getSelectedTicketIds().length;
        $('#bulk-status-count').text(selectedCount);
        $('#bulkStatusModal').modal('show');
      }

      function showBulkPriorityModal() {
        var selectedCount = getSelectedTicketIds().length;
        $('#bulk-priority-count').text(selectedCount);
        $('#bulkPriorityModal').modal('show');
      }

      function showBulkCategoryModal() {
        var selectedCount = getSelectedTicketIds().length;
        $('#bulk-category-count').text(selectedCount);
        $('#bulkCategoryModal').modal('show');
      }

      // Execute bulk operations
      function executeBulkAssign() {
        var ticketIds = getSelectedTicketIds();
        var assignedTo = $('#bulk-assign-user').val();

        if (!assignedTo) {
          alert('Please select a user to assign tickets to.');
          return;
        }

        performBulkOperation('{{ route("tickets.bulk.assign") }}', {
          ticket_ids: ticketIds,
          assigned_to: assignedTo
        }, '#bulkAssignModal');
      }

      function executeBulkUpdateStatus() {
        var ticketIds = getSelectedTicketIds();
        var statusId = $('#bulk-status').val();

        if (!statusId) {
          alert('Please select a status.');
          return;
        }

        performBulkOperation('{{ route("tickets.bulk.update-status") }}', {
          ticket_ids: ticketIds,
          status_id: statusId
        }, '#bulkStatusModal');
      }

      function executeBulkUpdatePriority() {
        var ticketIds = getSelectedTicketIds();
        var priorityId = $('#bulk-priority').val();

        if (!priorityId) {
          alert('Please select a priority.');
          return;
        }

        performBulkOperation('{{ route("tickets.bulk.update-priority") }}', {
          ticket_ids: ticketIds,
          priority_id: priorityId
        }, '#bulkPriorityModal');
      }

      function executeBulkUpdateCategory() {
        var ticketIds = getSelectedTicketIds();
        var typeId = $('#bulk-category').val();

        if (!typeId) {
          alert('Please select a category.');
          return;
        }

        performBulkOperation('{{ route("tickets.bulk.update-category") }}', {
          ticket_ids: ticketIds,
          type_id: typeId
        }, '#bulkCategoryModal');
      }

      function confirmBulkDelete() {
        var ticketIds = getSelectedTicketIds();
        
        if (confirm('Are you sure you want to delete ' + ticketIds.length + ' ticket(s)? This action cannot be undone.')) {
          performBulkOperation('{{ route("tickets.bulk.delete") }}', {
            ticket_ids: ticketIds
          }, null);
        }
      }

      // Generic function to perform bulk operations
      function performBulkOperation(url, data, modalId) {
        $.ajax({
          url: url,
          type: 'POST',
          data: data,
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          beforeSend: function() {
            // Show loading overlay
            showLoading('Processing request...');
            // Disable buttons
            $('button').prop('disabled', true);
          },
          success: function(response) {
            if (modalId) {
              $(modalId).modal('hide');
            }
            
            alert(response.message);
            
            // Reload page to show updated data
            window.location.reload();
          },
          error: function(xhr) {
            var errorMessage = 'An error occurred';
            if (xhr.responseJSON && xhr.responseJSON.message) {
              errorMessage = xhr.responseJSON.message;
            }
            alert('Error: ' + errorMessage);
          },
          complete: function() {
            // Hide loading overlay
            hideLoading();
            // Re-enable buttons
            $('button').prop('disabled', false);
          }
        });
      }
    </script>

@include('components.loading-overlay')

@endsection


