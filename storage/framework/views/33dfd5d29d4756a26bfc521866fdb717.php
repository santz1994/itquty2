

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
                <th>Asset Type Name</th>
                <th>Abbreviation</th>
                <th>Tracking Spare</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__currentLoopData = $asset_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <div>
                    <td><?php echo e($asset_type->type_name); ?></td>
                    <td><?php echo e($asset_type->abbreviation); ?></td>
                    <td>
                      <?php if($asset_type->spare == 1): ?>
                        Yes
                      <?php else: ?>
                        No
                      <?php endif; ?>
                    </td>
                    <td>
                      <div class="btn-group">
                        <a href="/asset-types/<?php echo e($asset_type->id); ?>/edit" class="btn btn-primary"><span class='fa fa-pencil' aria-hidden='true'></span> <b>Edit</b></a>
                        <form method="POST" action="<?php echo e(url('asset-types/' . $asset_type->id)); ?>" style="display:inline-block; margin-left:6px;" onsubmit="return confirm('Are you sure you want to delete this asset type?');">
                          <?php echo e(csrf_field()); ?>

                          <?php echo e(method_field('DELETE')); ?>

                          <button type="submit" class="btn btn-danger"><span class="fa fa-trash" aria-hidden="true"></span> <b>Delete</b></button>
                        </form>
                      </div>
                    </td>
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
          <h3 class="box-title">Create New Asset Type</h3>
        </div>
        <div class="box-body">
          <form method="POST" action="<?php echo e(url('asset-types')); ?>">
            <?php echo e(csrf_field()); ?>

            <div class="form-group <?php echo e(hasErrorForClass($errors, 'type_name')); ?>">
              <label for="type_name">Asset Type Name</label>
              <input type="text" name="type_name" class="form-control" value="<?php echo e(old('type_name')); ?>">
              <?php echo e(hasErrorForField($errors, 'type_name')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'abbreviation')); ?>">
              <label for="abbreviation">Abbreviation</label>
              <input type="text"  name="abbreviation" class="form-control" value="<?php echo e(old('abbreviation')); ?>">
              <?php echo e(hasErrorForField($errors, 'abbreviation')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'spare')); ?>">
              <label for="spare">Track Spare Level?</label>
              <select class="form-control spare" name="spare">
                <option value = 0>No</option>
                <option value = 1>Yes</option>
              </select>
              <?php echo e(hasErrorForField($errors, 'spare')); ?>

            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary"><b>Add New Asset Type</b></button>
            </div>
          </form>
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



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\asset-types\index.blade.php ENDPATH**/ ?>