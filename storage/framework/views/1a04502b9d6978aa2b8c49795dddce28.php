

<?php $__env->startSection('main-content'); ?>


<?php echo $__env->make('components.page-header', [
    'title' => $pageTitle ?? 'Edit Asset',
    'subtitle' => 'Update asset information - ' . ($asset->asset_tag ?? ''),
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home'), 'icon' => 'home'],
        ['label' => 'Assets', 'url' => route('assets.index')],
        ['label' => 'Edit']
    ],
    'actions' => '<a href="'.route('assets.show', $asset->id).'" class="btn btn-info">
        <i class="fa fa-eye"></i> View Asset
    </a>
    <a href="'.route('assets.index').'" class="btn btn-secondary">
        <i class="fa fa-arrow-left"></i> Back to List
    </a>'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  <div class="row">
    <div class="col-md-6 col-md-offset-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Asset Information</h3>
        </div>
        <div class="box-body">
          <form method="POST" action="/assets/<?php echo e($asset->id); ?>" id="asset-edit-form">
            <?php echo e(method_field('PATCH')); ?>

            <?php echo e(csrf_field()); ?>

            <div class="form-group">
              <label for="serial_number">Serial Number</label>
              <input type="text" name="serial_number" id="serial_number" class="form-control" value="<?php echo e($asset->serial_number); ?>">
            </div>
            <div class="form-group">
              <label for="model_id">Model</label>
              <select class="form-control model_id" name="model_id" id="model_id" required>
                <?php $__currentLoopData = $asset_models; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset_model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option
                    <?php if($asset->model_id == $asset_model->id): ?>
                      selected
                    <?php endif; ?>
                  value="<?php echo e($asset_model->id); ?>"><?php echo e($asset_model->manufacturer->name); ?> - <?php echo e($asset_model->asset_model); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="division_id">Division</label>
              <select class="form-control division_id" name="division_id" id="division_id">
                <?php $__currentLoopData = $divisions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $division): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option
                    <?php if($asset->division_id == $division->id): ?>
                      selected
                    <?php endif; ?>
                  value="<?php echo e($division->id); ?>"><?php echo e($division->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="supplier_id">Supplier</label>
              <select class="form-control supplier_id" name="supplier_id" id="supplier_id">
                <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option
                    <?php if($asset->supplier_id == $supplier->id): ?>
                      selected
                    <?php endif; ?>
                  value="<?php echo e($supplier->id); ?>"><?php echo e($supplier->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="invoice_id">Invoice</label>
              <select class="form-control invoice_id" name="invoice_id" id="invoice_id">
                <option value=""></option>
                <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option
                    <?php if($asset->invoice_id == $invoice->id): ?>
                      selected
                    <?php endif; ?>
                  value="<?php echo e($invoice->id); ?>"><?php echo e($invoice->invoice_number); ?> - <?php echo e($invoice->invoiced_date); ?> - <?php echo e($invoice->supplier->name); ?> - R<?php echo e($invoice->total); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="purchase_date">Purchase Date</label>
1              <input type="date" name="purchase_date" class="form-control" id="purchase_date" value="<?php echo e(old('purchase_date', optional($asset->purchase_date)->format('Y-m-d'))); ?>">
            </div>
            <div class="form-group">
              <label for="warranty_months">Warranty Months</label>
              <input type="number" min="0" name="warranty_months" class="form-control" id="warranty_months" value="<?php echo e(old('warranty_months', $asset->warranty_months)); ?>">
            </div>
            <div class="form-group">
              <label for="warranty_type_id">Warranty Type</label>
              <select class="form-control warranty_type_id" name="warranty_type_id" id="warranty_type_id">
                <option value=""></option>
                <?php $__currentLoopData = $warranty_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warranty_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option
                    <?php if($asset->warranty_type_id == $warranty_type->id): ?>
                      selected
                    <?php endif; ?>
                  value="<?php echo e($warranty_type->id); ?>"><?php echo e($warranty_type->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="ip_address">IP Address (If PC/Laptop)</label>
              <input type="text" name="ip_address" class="form-control" id="ip_address" value="<?php echo e(old('ip_address', $asset->ip_address)); ?>">
            </div>
            <div class="form-group">
              <label for="mac_address">MAC Address (If PC/Laptop)</label>
              <input type="text" name="mac_address" class="form-control" id="mac_address" value="<?php echo e(old('mac_address', $asset->mac_address)); ?>">
            </div>

            <div class="form-group">
              <label for="status_id">Status <span class="text-red">*</span></label>
              <select class="form-control status_id" name="status_id" id="status_id" required>
                <option value="">Select Status</option>
                <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option
                    <?php if($asset->status_id == $status->id): ?>
                      selected
                    <?php endif; ?>
                  value="<?php echo e($status->id); ?>"><?php echo e($status->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

            <div class="form-group">
              <label for="location_id">Location</label>
              <select class="form-control location_id" name="location_id" id="location_id">
                <option value="">No Location</option>
                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option
                    <?php if($asset->location_id == $location->id): ?>
                      selected
                    <?php endif; ?>
                  value="<?php echo e($location->id); ?>"><?php echo e($location->location_name); ?> - <?php echo e($location->building); ?>, <?php echo e($location->office); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary btn-lg">
                <i class="fa fa-save"></i> <b>Update Asset</b>
              </button>
              <a href="<?php echo e(route('assets.show', $asset->id)); ?>" class="btn btn-info btn-lg">
                <i class="fa fa-eye"></i> View
              </a>
              <a href="<?php echo e(route('assets.index')); ?>" class="btn btn-secondary btn-lg">
                <i class="fa fa-times"></i> Cancel
              </a>
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
  </div>


<?php echo $__env->make('components.loading-overlay', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">
  // Form loading state
  $('#asset-edit-form').on('submit', function() {
    showLoading('Updating asset...');
  });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('footer'); ?>
  <script type="text/javascript">
    $(document).ready(function() {
      $(".model_id").select2();
      $(".division_id").select2();
      $(".supplier_id").select2();
      $(".invoice_id").select2();
      $(".warranty_type_id").select2();
      $(".status_id").select2();
      $(".location_id").select2();
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



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/assets/edit.blade.php ENDPATH**/ ?>