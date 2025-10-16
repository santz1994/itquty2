

<?php $__env->startSection('main-content'); ?>
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
                <th>Building</th>
                <th>Office</th>
                <th>Name</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <div>
                    <td><?php echo e($location->building); ?></td>
                    <td><?php echo e($location->office); ?></td>
                    <td><?php echo e($location->location_name); ?></td>
                    <td><a href="/locations/<?php echo e($location->id); ?>/edit" class="btn btn-primary"><span class='fa fa-pencil' aria-hidden='true'></span> <b>Edit</b></a></td>
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
          <h3 class="box-title">Create New Location</h3>
        </div>
        <div class="box-body">
          <form method="POST" action="<?php echo e(url('locations')); ?>">
            <?php echo e(csrf_field()); ?>

            <div class="form-group <?php echo e(hasErrorForClass($errors, 'building')); ?>">
              <label for="building">Building</label>
              <input type="text" name="building" class="form-control" value="<?php echo e(old('building')); ?>">
              <?php echo e(hasErrorForField($errors, 'building')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'office')); ?>">
              <label for="office">Office</label>
              <input type="text"  name="office" class="form-control" value="<?php echo e(old('office')); ?>">
              <?php echo e(hasErrorForField($errors, 'office')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'location_name')); ?>">
              <label for="location_name">Location Name</label>
              <input type="text"  name="location_name" class="form-control" value="<?php echo e(old('location_name')); ?>">
              <?php echo e(hasErrorForField($errors, 'location_name')); ?>

            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary"><b>Add New Location</b></button>
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
        order: [[ 2, "asc" ]]
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



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/locations/index.blade.php ENDPATH**/ ?>