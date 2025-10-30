

<?php $__env->startSection('main-content'); ?>
  <div class="row">
    <div class="col-md-4 col-md-offset-2">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><?php echo e($pageTitle); ?></h3>
        </div>
        <div class="box-body">
          <form method="POST" action="/assets/<?php echo e($asset->id); ?>/store">
            <?php echo e(csrf_field()); ?>

            <div class="form-group">
              <label for="location_id">Location</label>
              <select class="form-control location_id" name="location_id">
                <option value = ""></option>
                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($location->id); ?>"><?php echo e($location->building); ?>, <?php echo e($location->office); ?>, <?php echo e($location->location_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="status_id">Status</label>
              <select class="form-control status_id" name="status_id">
                <option value = ""></option>
                <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($status->id); ?>"><?php echo e($status->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary"><b>Move Asset</b></button>
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
    <div class="col-md-4">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Current Location/Status</h3>
        </div>
        <div class="box-body">
            <h4><b>Location:</b> <?php echo e($asset->movement->location->location_name); ?></h4>
            <h4><b>Status:</b> <?php echo e($asset->movement->status->name); ?></h4>
        </div>
      </div>
    </div>
    </div>
    <div class="text-center"><a class="btn btn-primary" href="<?php echo e(URL::previous()); ?>">Back</a></div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer'); ?>
  <script type="text/javascript">
    $(document).ready(function() {
      $(".location_id").select2();
      $(".status_id").select2();
    });
  </script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\movements\move.blade.php ENDPATH**/ ?>