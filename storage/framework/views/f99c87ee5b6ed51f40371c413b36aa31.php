

<?php $__env->startSection('main-content'); ?>

<?php echo $__env->make('components.page-header', ['title' => 'Import Results', 'subtitle' => 'Recent import job results'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="row">
  <div class="col-md-12">
    <div class="box box-primary">
      <div class="box-header with-border"><h3 class="box-title">Results</h3></div>
      <div class="box-body">
        <?php if(empty($files)): ?>
          <p>No result files found.</p>
        <?php else: ?>
          <table class="table table-striped">
            <thead><tr><th>File</th><th>Modified</th><th>Action</th></tr></thead>
            <tbody>
              <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td><?php echo e(basename($f)); ?></td>
                  <td><?php echo e(date('Y-m-d H:i:s', filemtime(storage_path('app/' . $f)))); ?></td>
                  <td><a class="btn btn-sm btn-default" href="<?php echo e(route('masterdata.results.download', basename($f))); ?>">Download</a></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\admin\masterdata\results.blade.php ENDPATH**/ ?>