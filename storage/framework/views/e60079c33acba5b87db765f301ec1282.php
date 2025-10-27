

<?php $__env->startSection('main-content'); ?>

<?php echo $__env->make('components.page-header', [
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
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

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
                  <?php if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin')): ?>
                  <button type="button" class="btn btn-sm btn-danger" onclick="confirmBulkDelete()">
                    <i class="fa fa-trash"></i> Delete
                  </button>
                  <?php endif; ?>
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
                <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($status->id); ?>" <?php echo e(request('status') == $status->id ? 'selected' : ''); ?>>
                    <?php echo e($status->status); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="form-group" style="margin-left: 10px;">
              <label for="priority">Priority:</label>
              <select name="priority" id="priority" class="form-control" onchange="this.form.submit()">
                <option value="">All Priorities</option>
                <?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($priority->id); ?>" <?php echo e(request('priority') == $priority->id ? 'selected' : ''); ?>>
                    <?php echo e($priority->priority); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="form-group" style="margin-left: 10px;">
              <label for="asset_id">Asset:</label>
              <select name="asset_id" id="asset_id" class="form-control" onchange="this.form.submit()">
                <option value="">All Assets</option>
                <?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($asset->id); ?>" <?php echo e(request('asset_id') == $asset->id ? 'selected' : ''); ?>>
                    <?php echo e($asset->model_name ? $asset->model_name : 'Unknown Model'); ?> (<?php echo e($asset->asset_tag); ?>)
                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <?php if(!auth()->user()->hasRole('user')): ?>
            <div class="form-group" style="margin-left: 10px;">
              <label for="assigned_to">Assigned To:</label>
              <select name="assigned_to" id="assigned_to" class="form-control" onchange="this.form.submit()">
                <option value="">All Admins</option>
                <?php $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($admin->id); ?>" <?php echo e(request('assigned_to') == $admin->id ? 'selected' : ''); ?>>
                    <?php echo e($admin->name); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <?php endif; ?>
            <div class="form-group" style="margin-left: 10px;">
              <input type="text" name="search" placeholder="Search tickets..." class="form-control" value="<?php echo e(request('search')); ?>">
            </div>
            <button type="submit" class="btn btn-primary" style="margin-left: 10px;">Filter</button>
            <a href="<?php echo e(route('tickets.index')); ?>" class="btn btn-default" style="margin-left: 5px;">Clear</a>
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
                <?php if(!auth()->user()->hasRole('user')): ?>
                  <th class="sortable" data-column="assigned_to">Assigned To</th>
                <?php endif; ?>
                <th class="actions">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <div>
                    <td>
                      <input type="checkbox" class="ticket-checkbox" value="<?php echo e($ticket->id); ?>" onchange="updateBulkToolbar()">
                    </td>
                    <td><?php echo e($ticket->ticket_code); ?></td>
                    <td><div class="hover-pointer" id="agent<?php echo e($ticket->id); ?>"><?php echo e($ticket->user->name); ?></div></td>
                    <td><div class="hover-pointer" id="location<?php echo e($ticket->id); ?>"><?php echo e($ticket->location->location_name); ?></div></td>
                    <td><div class="hover-pointer" id="asset<?php echo e($ticket->id); ?>">
                      <?php if($ticket->asset): ?>
                        <?php echo e($ticket->asset->name); ?> (<?php echo e($ticket->asset->asset_tag); ?>)
                      <?php else: ?>
                        <span class="text-muted">No Asset</span>
                      <?php endif; ?>
                    </div></td>
                    <td>
                      <div class="hover-pointer" id="status<?php echo e($ticket->id); ?>">
                        <?php if($ticket->ticket_status->status == 'Open'): ?>
                          <span class="label label-success">
                        <?php elseif($ticket->ticket_status->status == 'Pending'): ?>
                          <span class="label label-info">
                        <?php elseif($ticket->ticket_status->status == 'Resolved'): ?>
                          <span class="label label-warning">
                        <?php elseif($ticket->ticket_status->status == 'Closed'): ?>
                          <span class="label label-danger">
                        <?php endif; ?>
                        <?php echo e($ticket->ticket_status->status); ?></span>
                      </div>
                    </td>
                    <td>
                      <div class="hover-pointer" id="priority<?php echo e($ticket->id); ?>">
                        <?php if($ticket->ticket_priority->priority == 'Low'): ?>
                          <span class="label label-success">
                        <?php elseif($ticket->ticket_priority->priority == 'Medium'): ?>
                          <span class="label label-warning">
                        <?php elseif($ticket->ticket_priority->priority == 'High'): ?>
                          <span class="label label-danger">
                        <?php endif; ?>
                        <?php echo e($ticket->ticket_priority->priority); ?></span>
                      </div>
                    </td>
                      <td><?php echo e($ticket->subject); ?></td>
                      <?php if(!auth()->user()->hasRole('user')): ?>
                        <td>
                          <div class="hover-pointer" id="assigned<?php echo e($ticket->id); ?>">
                            <?php if($ticket->assignedTo): ?>
                              <?php echo e($ticket->assignedTo->name); ?>

                            <?php else: ?>
                              <span class="text-muted">Unassigned</span>
                            <?php endif; ?>
                          </div>
                        </td>
                      <?php endif; ?>
                      <td><a href="/tickets/<?php echo e($ticket->id); ?>" class="btn btn-primary"><span class="fa fa-ticket" aria-hidden="true"></span> <b>View</b></a></td>
                  </div>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
        <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          // Agent
          var agent = (function() {
            var x = '#agent' + <?php echo e($ticket->id); ?>;
            return x;
          });
          $(agent()).click(function () {
            table.search( "<?php echo e($ticket->user->name); ?>" ).draw();
          });

          // Location
          var location = (function() {
            var x = '#location' + <?php echo e($ticket->id); ?>;
            return x;
          });
          $(location()).click(function () {
            table.search( "<?php echo e($ticket->location->location_name); ?>" ).draw();
          });

          // Asset
          var asset = (function() {
            var x = '#asset' + <?php echo e($ticket->id); ?>;
            return x;
          });
          $(asset()).click(function () {
            <?php if($ticket->asset): ?>
              table.search( "<?php echo e($ticket->asset->asset_tag); ?>" ).draw();
            <?php endif; ?>
          });

          // Status
          var status = (function() {
            var x = '#status' + <?php echo e($ticket->id); ?>;
            return x;
          });
          $(status()).click(function () {
            table.search( "<?php echo e($ticket->ticket_status->status); ?>" ).draw();
          });

          // Priority
          var priority = (function() {
            var x = '#priority' + <?php echo e($ticket->id); ?>;
            return x;
          });
          $(priority()).click(function () {
            table.search( "<?php echo e($ticket->ticket_priority->priority); ?>" ).draw();
          });
            <?php if(!auth()->user()->hasRole('user')): ?>
            // Assigned To
            var assigned = (function() {
              var x = '#assigned' + <?php echo e($ticket->id); ?>;
              return x;
            });
            $(assigned()).click(function () {
              <?php if($ticket->assignedTo): ?>
                table.search( "<?php echo e($ticket->assignedTo->name); ?>" ).draw();
              <?php endif; ?>
            });
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
          url: '<?php echo e(route("tickets.bulk.options")); ?>',
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

        performBulkOperation('<?php echo e(route("tickets.bulk.assign")); ?>', {
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

        performBulkOperation('<?php echo e(route("tickets.bulk.update-status")); ?>', {
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

        performBulkOperation('<?php echo e(route("tickets.bulk.update-priority")); ?>', {
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

        performBulkOperation('<?php echo e(route("tickets.bulk.update-category")); ?>', {
          ticket_ids: ticketIds,
          type_id: typeId
        }, '#bulkCategoryModal');
      }

      function confirmBulkDelete() {
        var ticketIds = getSelectedTicketIds();
        
        if (confirm('Are you sure you want to delete ' + ticketIds.length + ' ticket(s)? This action cannot be undone.')) {
          performBulkOperation('<?php echo e(route("tickets.bulk.delete")); ?>', {
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

<?php echo $__env->make('components.loading-overlay', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/tickets/index.blade.php ENDPATH**/ ?>