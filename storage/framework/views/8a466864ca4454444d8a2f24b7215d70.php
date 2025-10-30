

<?php $__env->startSection('main-content'); ?>
  <div class="row">
    <div class="col-md-6">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Edit Ticket Status</h3>
        </div>
        <div class="box-body">
          <form method="POST" action="/admin/ticket-statuses/<?php echo e($ticketsStatus->id); ?>">
            <?php echo e(method_field('PATCH')); ?>

            <?php echo e(csrf_field()); ?>

            <div class="form-group <?php echo e(hasErrorForClass($errors, 'status')); ?>">
              <label for="status">Status</label>
              <input type="text" name="status" class="form-control" value="<?php echo e($ticketsStatus->status); ?>">
              <?php echo e(hasErrorForField($errors, 'status')); ?>

            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary"><b>Edit Ticket Status</b></button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Ticket Statuses</h3>
        </div>
        <div class="box-body">
          <ul>
            <?php $__currentLoopData = $ticketsStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li><?php echo e($status->status); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        </div>
      </div>
    </div>
  </div>
  <?php $__env->stopSection(); ?>
  <?php if(Session::has('status')): ?>
    <div class="alert alert-success" style="margin-top:10px;">
      <?php echo e(Session::get('message')); ?>

    </div>
    <script>
      $(document).ready(function() {
        Command: toastr["<?php echo e(Session::get('status')); ?>"]("<?php echo e(Session::get('message')); ?>", "<?php echo e(Session::get('title')); ?>");
      });
    </script>
  <?php endif; ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\admin\ticket-statuses\edit.blade.php ENDPATH**/ ?>