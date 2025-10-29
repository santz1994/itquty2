

<?php $__env->startSection('main-content'); ?>
  <div class="row">
    <div class="col-md-6 col-md-offset-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><?php echo e($pageTitle); ?></h3>
        </div>
        <div class="box-body">
          <form method="POST" action="/divisions/<?php echo e($division->id); ?>">
            <?php echo e(method_field('PATCH')); ?>

            <?php echo e(csrf_field()); ?>

            <div class="form-group">
              <label for="name">Division Name</label>
              <input type="text" name="name" class="form-control" value="<?php echo e($division->name); ?>">
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary"><b>Edit Division</b></button>
            </div>
          </form>
        </div>
      </div>

      <?php if(count($errors)): ?>
        <ul>
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/divisions/edit.blade.php ENDPATH**/ ?>