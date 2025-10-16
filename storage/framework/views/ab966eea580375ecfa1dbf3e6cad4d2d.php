

<?php $__env->startSection('main-content'); ?>
  
  Successfully created
  <div class="row">
    <div class="col-md-9">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><?php echo e($pageTitle); ?></h3>
        </div>
        <div class="box-body">
          <table id="table" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th>CPU</th>
                <th>RAM</th>
                <th>HDD/SSD</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__currentLoopData = $pcspecs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pcspec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <div>
                    <td><?php echo e($pcspec->cpu); ?></td>
                    <td><?php echo e($pcspec->ram); ?></td>
                    <td><?php echo e($pcspec->hdd); ?></td>
                    <td><a href="/pcspecs/<?php echo e($pcspec->id); ?>/edit" class="btn btn-primary"><span class='fa fa-pencil' aria-hidden='true'></span> <b>Edit</b></a></td>
                  </div>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Create New PC Specification</h3>
        </div>
        <div class="box-body">
          <form method="POST" action="<?php echo e(url('pcspecs')); ?>">
            <?php echo e(csrf_field()); ?>

            <div class="form-group <?php echo e(hasErrorForClass($errors, 'cpu')); ?>">
              <label for="cpu">CPU</label>
              <input type="text" name="cpu" class="form-control" value="<?php echo e(old('cpu')); ?>">
              <?php echo e(hasErrorForField($errors, 'cpu')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'ram')); ?>">
              <label for="ram">RAM</label>
              <input type="text"  name="ram" class="form-control" value="<?php echo e(old('ram')); ?>">
              <?php echo e(hasErrorForField($errors, 'ram')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'hdd')); ?>">
              <label for="hdd">HDD/SSD</label>
              <input type="text"  name="hdd" class="form-control" value="<?php echo e(old('hdd')); ?>">
              <?php echo e(hasErrorForField($errors, 'hdd')); ?>

            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary"><b>Add New PC Specification</b></button>
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
          orderable: false, targets: 3
        } ],
        order: [[ 0, "asc" ]]
      } );
    } );
  </script>
  <?php if(Session::has('status')): ?>
    <script>
      $(document).ready(function() {
        Command: toastr["<?php echo e(Session::get('status')); ?>"]("<?php echo e(Session::get('message')); ?>", "<?php echo e(Session::get('title')); ?>");
      });
    </script>
  <?php endif; ?>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/pcspecs/index.blade.php ENDPATH**/ ?>