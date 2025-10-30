

<?php $__env->startSection('main-content'); ?>
  <div class="row">
    <div class="col-md-6">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Edit Ticket Type</h3>
        </div>
        <div class="box-body">
          <form method="POST" action="/admin/ticket-types/<?php echo e($ticketsType->id); ?>">
            <?php echo e(method_field('PATCH')); ?>

            <?php echo e(csrf_field()); ?>

            <div class="form-group <?php echo e(hasErrorForClass($errors, 'type')); ?>">
              <label for="type">Type</label>
              <input type="text" name="type" class="form-control" value="<?php echo e($ticketsType->type); ?>">
              <?php echo e(hasErrorForField($errors, 'type')); ?>

            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary"><b>Edit Ticket Type</b></button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Ticket Types</h3>
        </div>
        <div class="box-body">
          <ul>
            <?php $__currentLoopData = $ticketsTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li><?php echo e($type->type); ?></li>
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



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\admin\ticket-types\edit.blade.php ENDPATH**/ ?>