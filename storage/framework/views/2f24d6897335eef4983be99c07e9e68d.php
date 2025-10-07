

<?php $__env->startSection('main-content'); ?>
  <div class="row">
    <div class="col-md-9">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><?php echo e($pageTitle); ?></h3>
        </div>
        <div class="box-body">
          <table id="table" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th>Ticket Priority</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__currentLoopData = $ticketsPriorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticketsPriority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <div>
                    <td><?php echo e($ticketsPriority->priority); ?></td>
                    <td><a href="/admin/ticket-priorities/<?php echo e($ticketsPriority->id); ?>/edit" class="btn btn-primary"><span class='fa fa-pencil' aria-hidden='true'></span> <b>Edit</b></a></td>
                  </div>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Create Ticket Priority</h3>
        </div>
        <div class="box-body">
          <form method="POST" action="<?php echo e(url('admin/ticket-priorities')); ?>">
            <?php echo e(csrf_field()); ?>

            <div class="form-group <?php echo e(hasErrorForClass($errors, 'priority')); ?>">
              <label for="priority">Priority</label>
              <input type="text"  name="priority" class="form-control" value="<?php echo e(old('priority')); ?>">
              <?php echo e(hasErrorForField($errors, 'priority')); ?>

            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary"><b>Add New Ticket Priority</b></button>
            </div>
          </form>
        </div>
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

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/admin/ticket-priorities/index.blade.php ENDPATH**/ ?>