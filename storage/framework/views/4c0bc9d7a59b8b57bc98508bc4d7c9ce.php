

<?php $__env->startSection('main-content'); ?>
  <div class="row">
    <div class="col-md-6 col-md-offset-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><?php echo e($pageTitle); ?></h3>
        </div>
        <div class="box-body">
          <form method="POST" action="/budgets/<?php echo e($budget->id); ?>">
            <?php echo e(method_field('PATCH')); ?>

            <?php echo e(csrf_field()); ?>

            <div class="form-group <?php echo e(hasErrorForClass($errors, 'division_id')); ?>">
              <label for="division_id">Division</label>
              <select class="form-control division_id" name="division_id">
                <?php $__currentLoopData = $divisions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $division): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option
                    <?php if($budget->division_id == $division->id): ?>
                      selected
                    <?php endif; ?>
                  value="<?php echo e($division->id); ?>"><?php echo e($division->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php echo e(hasErrorForField($errors, 'division_id')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'year')); ?>">
              <label for="year">Year</label>
              <input type="text"  name="year" class="form-control" value="<?php echo e($budget->year); ?>">
              <?php echo e(hasErrorForField($errors, 'year')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'total')); ?>">
              <label for="total">Budget Total</label>
              <div class="input-group">
                <div class="input-group-addon">R</div>
                <input type="text"  name="total" class="form-control" value="<?php echo e($budget->total); ?>">
                <?php echo e(hasErrorForField($errors, 'total')); ?>

              </div>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary"><b>Edit Budget</b></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('footer'); ?>
  <script type="text/javascript">
    $(document).ready(function() {
      $(".division_id").select2();
    });
  </script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\budgets\edit.blade.php ENDPATH**/ ?>