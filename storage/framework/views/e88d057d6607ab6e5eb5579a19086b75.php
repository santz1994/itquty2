

<?php $__env->startSection('main-content'); ?>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><?php echo e($pageTitle); ?></h3>
        </div>
        <div class="box-body">
          <p><a href="tickets/create"><button type="button" class="btn btn-default" name="create-new-ticket" data-toggle="tooltip" data-original-title="Create New Ticket"><span class='fa fa-plus' aria-hidden='true'></span> <b>Create New Ticket</b></button></a></p>
          
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
                <?php if(!auth()->user()->hasRole('user')): ?>
                  <th>Assigned To</th>
                <?php endif; ?>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <div>
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
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/tickets/index.blade.php ENDPATH**/ ?>