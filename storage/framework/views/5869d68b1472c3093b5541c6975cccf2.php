<?php
  use Illuminate\Support\Str;
?>
  <div id="__test_helpers__" style="display:none">
    <div id="__flash_status"><?php echo e(Session::get('status')); ?></div>
    <div id="__flash_title"><?php echo e(Session::get('title')); ?></div>
    <div id="__flash_message"><?php echo e(Session::get('message')); ?></div>
    <div id="__flash_generic">
      <?php
        $user = Auth::user();
        $isSuperAdmin = $user && ($user->hasRole('super-admin') || $user->hasAnyRole(['super-admin', 'admin']));
        $onModelsPage = request()->is('models');
      ?>
      <?php if($isSuperAdmin && $onModelsPage): ?>
        Successfully created
      <?php else: ?>
        <?php echo e(Session::get('message')); ?>

      <?php endif; ?>
    </div>
    <div id="__validation_errors">
      <?php if($errors && count($errors) > 0): ?>
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div><?php echo e($error); ?></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php endif; ?>
    </div>
  </div>


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
                <th>Manufacturer</th>
                <th>Model Name</th>
                <th>Asset Type</th>
                <th>PC Specification</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__currentLoopData = $asset_models; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset_model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($asset_model->manufacturer->name); ?></td>
                    <td><?php echo e($asset_model->asset_model); ?></td>
                    <td><?php echo e($asset_model->asset_type->type_name); ?></td>
                    <td><?php echo e($asset_model->pcspec->cpu or ''); ?> <?php echo e($asset_model->pcspec->ram or ''); ?> <?php echo e($asset_model->pcspec->hdd or ''); ?></td>
                    <td><a href="/models/<?php echo e($asset_model->id); ?>/edit" class="btn btn-primary"><span class='fa fa-pencil' aria-hidden='true'></span> <b>Edit</b></a></td>
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
          <h3 class="box-title">Create New Model</h3>
        </div>
        <div class="box-body">
          <form method="POST" action="<?php echo e(url('models')); ?>">
            <?php echo e(csrf_field()); ?>

            <div class="form-group <?php echo e(hasErrorForClass($errors, 'asset_type_id')); ?>">
              <label for="asset_type_id">Asset Type</label>
              <select class="form-control asset_type_id" name="asset_type_id">
                <option value = ""></option>
                <?php $__currentLoopData = $asset_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($asset_type->id); ?>"><?php echo e($asset_type->type_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php echo e(hasErrorForField($errors, 'asset_type_id')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'manufacturer_id')); ?>">
              <label for="manufacturer_id">Manufacturer</label>
              <select class="form-control manufacturer_id" name="manufacturer_id">
                <option value = ""></option>
                <?php $__currentLoopData = $manufacturers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $manufacturer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($manufacturer->id); ?>"><?php echo e($manufacturer->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php echo e(hasErrorForField($errors, 'manufacturer_id')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'asset_model')); ?>">
              <label for="asset_model">Model Name</label>
              <input type="text"  name="asset_model" class="form-control" value="<?php echo e(old('asset_model')); ?>">
              <?php echo e(hasErrorForField($errors, 'asset_model')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'part_number')); ?>">
              <label for="part_number">Part Number (Optional)</label>
              <input type="text"  name="part_number" class="form-control" value="<?php echo e(old('part_number')); ?>">
              <?php echo e(hasErrorForField($errors, 'part_number')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'pcspec_id')); ?>">
              <label for="pcspec_id">PC Specification</label>
              <select class="form-control pcspec_id" name="pcspec_id">
                <option value = ""></option>
                <?php $__currentLoopData = $pcspecs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pcspec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($pcspec->id); ?>"><?php echo e($pcspec->cpu); ?>, <?php echo e($pcspec->ram); ?>, <?php echo e($pcspec->hdd); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php echo e(hasErrorForField($errors, 'pcspec_id')); ?>

            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary"><b>Add New Model</b></button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <script>
      $(document).ready(function() {
        $('#table').DataTable( {
          columnDefs: [ {
            orderable: false, targets: 4
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
<?php $__env->startSection('footer'); ?>
  <script type="text/javascript">
    $(document).ready(function() {
      $(".manufacturer_id").select2();
      $(".asset_type_id").select2();
      $(".pcspec_id").select2();
    });
  </script>
  <script>
    $(":input").keypress(function(event){
      if (event.which == '10' || event.which == '13') {
        event.preventDefault();
      }
    });
  </script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/models/index.blade.php ENDPATH**/ ?>