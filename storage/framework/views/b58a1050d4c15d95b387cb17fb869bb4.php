

<?php $__env->startSection('main-content'); ?>
  <div class="row">
    <div class="col-md-6 col-md-offset-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><?php echo e($pageTitle); ?></h3>
        </div>
        <div class="box-body">
          <form method="POST" action="/locations/<?php echo e($location->id); ?>">
            <?php echo e(method_field('PATCH')); ?>

            <?php echo e(csrf_field()); ?>

            <div class="form-group <?php echo e(hasErrorForClass($errors, 'building')); ?>">
              <label for="building">Building</label>
              <input type="text" name="building" class="form-control" value="<?php echo e($location->building); ?>">
              <?php echo e(hasErrorForField($errors, 'building')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'office')); ?>">
              <label for="office">Office</label>
              <input type="text"  name="office" class="form-control" value="<?php echo e($location->office); ?>">
              <?php echo e(hasErrorForField($errors, 'office')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'location_name')); ?>">
              <label for="location_name">Location Name</label>
              <input type="text"  name="location_name" class="form-control" value="<?php echo e($location->location_name); ?>">
              <?php echo e(hasErrorForField($errors, 'location_name')); ?>

            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary"><b>Edit Location</b></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/locations/edit.blade.php ENDPATH**/ ?>