

<?php $__env->startSection('main-content'); ?>
  <div class="row">
    <div class="col-md-6">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><?php echo e($pageTitle); ?></h3>
        </div>
        <div class="box-body">
          <table id="table" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th>Subject</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__currentLoopData = $ticketsCannedFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticketCannedField): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <div>
                    <td><?php echo e($ticketCannedField->subject); ?></td>
                    <td><a href="/admin/ticket-canned-fields/<?php echo e($ticketCannedField->id); ?>/edit" class="btn btn-primary"><span class='fa fa-pencil' aria-hidden='true'></span> <b>Edit</b></a></td>
                  </div>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Create Canned Ticket Fields</h3>
        </div>
        <div class="box-body">
          <form method="POST" action="<?php echo e(url('admin/ticket-canned-fields')); ?>">
            <?php echo e(csrf_field()); ?>

            <div class="form-group <?php echo e(hasErrorForClass($errors, 'user_id')); ?>">
              <label for="user_id">Agent</label>
              <select class="form-control user_id" name="user_id">
                <option value = ""></option>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php echo e(hasErrorForField($errors, 'user_id')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'location_id')); ?>">
              <label for="location_id">Location</label>
              <select class="form-control location_id" name="location_id">
                <option value = ""></option>
                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($location->id); ?>"><?php echo e($location->location_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php echo e(hasErrorForField($errors, 'location_id')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'ticket_status_id')); ?>">
              <label for="ticket_status_id">Status</label>
              <select class="form-control ticket_status_id" name="ticket_status_id">
                <option value = ""></option>
                <?php $__currentLoopData = $ticketsStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticketStatus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($ticketStatus->id); ?>"><?php echo e($ticketStatus->status); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php echo e(hasErrorForField($errors, 'ticket_status_id')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'ticket_type_id')); ?>">
              <label for="ticket_type_id">Type</label>
              <select class="form-control ticket_type_id" name="ticket_type_id">
                <option value = ""></option>
                <?php $__currentLoopData = $ticketsTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticketType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($ticketType->id); ?>"><?php echo e($ticketType->type); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php echo e(hasErrorForField($errors, 'ticket_type_id')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'ticket_priority_id')); ?>">
              <label for="ticket_priority_id">Priority</label>
              <select class="form-control ticket_priority_id" name="ticket_priority_id">
                <option value = ""></option>
                <?php $__currentLoopData = $ticketsPriorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticketsPriority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($ticketsPriority->id); ?>"><?php echo e($ticketsPriority->priority); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php echo e(hasErrorForField($errors, 'ticket_priority_id')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'subject')); ?>">
              <label for="subject">Subject</label>
              <input type="text" class="form-control" name="subject" value="<?php echo e(old('subject')); ?>">
              <?php echo e(hasErrorForField($errors, 'subject')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'description')); ?>">
              <label for="description">Description</label>
              <textarea class="form-control" rows="5" name="description"><?php echo e(old('description')); ?></textarea>
              <?php echo e(hasErrorForField($errors, 'description')); ?>

            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary"><b>Add New Ticket Canned Fields</b></button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <script>
      $(document).ready(function() {
        $('#table').DataTable( {
          columnDefs: [ {
            orderable: false, targets: 1
          } ],
          order: [[ 0, "asc" ]]
        } );
      } );
    </script>
  <?php if(Session::has('status')): ?>
    <script>
      $(document).ready(function() {
        Command: toastr["<?php echo e(Session::get('status')); ?>"]("<?php echo e(Session::get('message')); ?>", "<?php echo e(Session::get('title')); ?>");
      });
    </script>
  <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('footer'); ?>
  <script type="text/javascript">
    $(document).ready(function() {
      $(".user_id").select2();
      $(".location_id").select2();
      $(".ticket_status_id").select2();
      $(".ticket_type_id").select2();
      $(".ticket_priority_id").select2();
    });
  </script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\admin\ticket-canned-fields\index.blade.php ENDPATH**/ ?>