

<?php $__env->startSection('main-content'); ?>
  <div class="row">
    <div class="col-md-6 col-md-offset-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><?php echo e($pageTitle); ?></h3>
        </div>
        <div class="box-body">
          <form method="POST" action="/manufacturers/<?php echo e($manufacturer->id); ?>">
            <?php echo e(method_field('PATCH')); ?>

            <?php echo e(csrf_field()); ?>

            <div class="form-group <?php echo e(hasErrorForClass($errors, 'name')); ?>">
              <label for="name">Manufacturer Name</label>
              <input type="text" name="name" class="form-control" value="<?php echo e($manufacturer->name); ?>">
              <?php echo e(hasErrorForField($errors, 'name')); ?>

            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary"><b>Edit Manufacturer</b></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  
  Successfully created
  <?php if(Session::has('status')): ?>
    <div><?php echo e(Session::get('message')); ?></div>
  <?php endif; ?>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\manufacturers\edit.blade.php ENDPATH**/ ?>