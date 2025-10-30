

<?php $__env->startSection('main-content'); ?>
  <div class="row">
    <div class="col-md-6 col-md-offset-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><?php echo e($pageTitle); ?></h3>
        </div>
        <div class="box-body">
          <form method="POST" action="/pcspecs/<?php echo e($pcspec->id); ?>">
            <?php echo e(method_field('PATCH')); ?>

            <?php echo e(csrf_field()); ?>

            <div class="form-group <?php echo e(hasErrorForClass($errors, 'cpu')); ?>">
              <label for="cpu">CPU</label>
              <input type="text" name="cpu" class="form-control" value="<?php echo e($pcspec->cpu); ?>">
              <?php echo e(hasErrorForField($errors, 'cpu')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'ram')); ?>">
              <label for="ram">RAM</label>
              <input type="text"  name="ram" class="form-control" value="<?php echo e($pcspec->ram); ?>">
              <?php echo e(hasErrorForField($errors, 'ram')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'hdd')); ?>">
              <label for="hdd">HDD</label>
              <input type="text"  name="hdd" class="form-control" value="<?php echo e($pcspec->hdd); ?>">
              <?php echo e(hasErrorForField($errors, 'hdd')); ?>

            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary"><b>Edit PC Specification</b></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  
  Core i3 5123
  <?php if(Session::has('status')): ?>
    <div><?php echo e(Session::get('message')); ?></div>
  <?php endif; ?>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\pcspecs\edit.blade.php ENDPATH**/ ?>