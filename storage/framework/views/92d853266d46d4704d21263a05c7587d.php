

<?php $__env->startSection('main-content'); ?>
  <div class="row">
    <div class="col-md-12 col-xs-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Spares</h3>
        </div>
        <div class="box-body">
          <table id="table" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th>Asset Type</th>
                <th>Division</th>
                <th>Quantity</th>
              </tr>
            </thead>
            <tbody>
              <?php $__currentLoopData = $assetTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assetType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $__currentLoopData = $divisions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $division): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php $quantity = $assetType->sparesCount($assetType->id, $division->id); ?>
                  <?php if($quantity >= 1): ?>
                    <tr>
                      <div>
                        <td><?php echo e($assetType->type_name); ?></td>
                        <td><?php echo e($division->name); ?></td>
                        <td><?php echo e($quantity); ?></td>
                      </div>
                    </tr>
                  <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <?php if(Session::has('status')): ?>
    <script>
      $(document).ready(function() {
        Command: toastr["<?php echo e(Session::get('status')); ?>"]("<?php echo e(Session::get('message')); ?>", "<?php echo e(Session::get('title')); ?>");
      });
    </script>
  <?php endif; ?>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/spares/index.blade.php ENDPATH**/ ?>