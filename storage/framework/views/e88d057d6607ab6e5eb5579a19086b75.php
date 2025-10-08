

<?php $__env->startSection('main-content'); ?>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><?php echo e($pageTitle); ?></h3>
        </div>
        <div class="box-body">
          <p><a href="tickets/create"><button type="button" class="btn btn-default" name="create-new-ticket" data-toggle="tooltip" data-original-title="Create New Ticket"><span class='fa fa-plus' aria-hidden='true'></span> <b>Create New Ticket</b></button></a></p>
          <table id="table" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th>Ticket Number</th>
                <th>Agent</th>
                <th>Location</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Subject</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <div>
                    <td><?php echo e($ticket->id); ?></td>
                    <td><div class="hover-pointer" id="agent<?php echo e($ticket->id); ?>"><?php echo e($ticket->user->name); ?></div></td>
                    <td><div class="hover-pointer" id="location<?php echo e($ticket->id); ?>"><?php echo e($ticket->location->location_name); ?></div></td>
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
            orderable: false, targets: 6
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
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      } );

    </script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/tickets/index.blade.php ENDPATH**/ ?>