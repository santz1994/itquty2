

<?php $__env->startSection('main-content'); ?>
  
  <div class="sr-legacy-marker" style="display:none">New Default Storeroom Saved</div>
  <div class="row">
    <div class="col-md-5">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><?php echo e($pageTitle); ?></h3>
        </div>
        <div class="box-body">
          <h4><b>Current Storeroom:</b>
            <?php if(isset($storeroom)): ?>
              <?php echo e($storeroom->location_name); ?>

            <?php else: ?>
              No Default Set. Please select the Default Storeroom
            <?php endif; ?> </h4>
            <form method="POST" action="<?php echo e(route('admin.storeroom.update')); ?>">
              <?php echo e(method_field('PATCH')); ?>

              <?php echo e(csrf_field()); ?>

              <div class="form-group <?php echo e(hasErrorForClass($errors, 'store')); ?>">
                <label for="store">Default Storeroom</label>
                <select class="form-control store" name="store">
                  <option value = ""></option>
                  <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($location->id); ?>"><?php echo e($location->building); ?> - <?php echo e($location->location_name); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php echo e(hasErrorForField($errors, 'store')); ?>

              </div>

              <div class="form-group">
                <button type="submit" class="btn btn-primary"><b>Set as Default Storeroom</b></button>
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
        toastr["<?php echo e(Session::get('status')); ?>"]("<?php echo e(Session::get('message')); ?>", "<?php echo e(Session::get('title')); ?>");
      });
    </script>
  <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer'); ?>
  <script type="text/javascript">
    $(document).ready(function() {
      $(".store").select2();
    });
  </script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\admin\storeroom\index.blade.php ENDPATH**/ ?>