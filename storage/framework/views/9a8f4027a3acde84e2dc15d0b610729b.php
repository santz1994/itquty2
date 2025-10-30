

<?php $__env->startSection('main-content'); ?>
  <div class="row">
    <div class="col-md-6 col-md-offset-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><?php echo e($pageTitle); ?></h3>
        </div>
        <div class="box-body">
            <form method="POST" action="/asset-types/<?php echo e($asset_type->id); ?>">
              <?php echo e(method_field('PATCH')); ?>

              <?php echo e(csrf_field()); ?>

              <div class="form-group <?php echo e(hasErrorForClass($errors, 'type_name')); ?>">
                <label for="type_name">Asset Type Name</label>
                <input type="text" name="type_name" class="form-control" value="<?php echo e($asset_type->type_name); ?>">
                <?php echo e(hasErrorForField($errors, 'type_name')); ?>

              </div>
              <div class="form-group <?php echo e(hasErrorForClass($errors, 'abbreviation')); ?>">
                <label for="abbreviation">Abbreviation</label>
                <input type="text"  name="abbreviation" class="form-control" value="<?php echo e($asset_type->abbreviation); ?>">
                <?php echo e(hasErrorForField($errors, 'abbreviation')); ?>

              </div>
              <div class="form-group <?php echo e(hasErrorForClass($errors, 'spare')); ?>">
                <label for="spare">Track Spare Level</label>
                <select class="form-control spare" name="spare">
                  <option
                    <?php if($asset_type->spare == 0): ?>
                      selected
                    <?php endif; ?>
                    value = 0>No
                  </option>
                  <option
                    <?php if($asset_type->spare == 1): ?>
                      selected
                    <?php endif; ?>
                    value = 1>Yes
                  </option>
                </select>
                <?php echo e(hasErrorForField($errors, 'spare')); ?>

              </div>

              <div class="form-group">
                <button type="submit" class="btn btn-primary"><b>Edit Asset Type</b></button>
              </div>
            </form>
          </div>
        </div>
    </div>
  </div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/asset-types/edit.blade.php ENDPATH**/ ?>