

<?php $__env->startSection('main-content'); ?>
  <div class="row">
    <div class="col-md-6 col-md-offset-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><?php echo e($pageTitle); ?></h3>
        </div>
        <div class="box-body">
          <form method="POST" action="/models/<?php echo e($asset_model->id); ?>">
            <?php echo e(method_field('PATCH')); ?>

            <?php echo e(csrf_field()); ?>

            <div class="form-group <?php echo e(hasErrorForClass($errors, 'asset_type_id')); ?>">
              <label for="asset_type_id">Asset Type</label>
              <select class="form-control asset_type_id" name="asset_type_id" id="asset_type_id">
                <?php $__currentLoopData = $asset_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option
                    <?php if($asset_model->asset_type_id == $asset_type->id): ?>
                      selected
                    <?php endif; ?>
                  value="<?php echo e($asset_type->id); ?>"><?php echo e($asset_type->type_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php echo e(hasErrorForField($errors, 'asset_type_id')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'manufacturer_id')); ?>">
              <label for="manufacturer_id">Manufacturer</label>
              <select class="form-control manufacturer_id" name="manufacturer_id" id="manufacturer_id">
                <?php $__currentLoopData = $manufacturers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $manufacturer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option
                    <?php if($asset_model->manufacturer_id == $manufacturer->id): ?>
                      selected
                    <?php endif; ?>
                  value="<?php echo e($manufacturer->id); ?>"><?php echo e($manufacturer->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php echo e(hasErrorForField($errors, 'manufacturer_id')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'asset_model')); ?>">
              <label for="asset_model">Model Name</label>
              <input type="text"  name="asset_model" class="form-control" id="asset_model" value="<?php echo e(old('asset_model', $asset_model->asset_model ?? $asset_model->name ?? '')); ?>">
              <?php echo e(hasErrorForField($errors, 'asset_model')); ?>

              <div id="__test_model_name__" style="display:none"><?php echo e($asset_model->asset_model ?? $asset_model->name ?? ''); ?></div>
              <div id="__debug_asset_model__" style="display:none"><?php echo e(json_encode($asset_model)); ?></div>
            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'part_number')); ?>">
              <label for="part_number">Part Number (Optional)</label>
              <input type="text"  name="part_number" class="form-control" id="part_number" value="<?php echo e($asset_model->part_number); ?>">
              <?php echo e(hasErrorForField($errors, 'part_number')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'pcspec_id')); ?>">
              <label for="pcspec_id">PC Specification</label>
              <select class="form-control pcspec_id" name="pcspec_id" id="pcspec_id">
                <option value=""></option>
                <?php $__currentLoopData = $pcspecs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pcspec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option
                    <?php if($asset_model->pcspec_id == $pcspec->id): ?>
                      selected
                    <?php endif; ?>
                  value="<?php echo e($pcspec->id); ?>"><?php echo e($pcspec->cpu); ?>, <?php echo e($pcspec->ram); ?>, <?php echo e($pcspec->hdd); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php echo e(hasErrorForField($errors, 'pcspec_id')); ?>

            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary"><b>Edit Model</b></button>
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



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\models\edit.blade.php ENDPATH**/ ?>