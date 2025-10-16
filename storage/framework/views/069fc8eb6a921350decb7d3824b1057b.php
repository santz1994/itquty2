

<?php $__env->startSection('main-content'); ?>


<?php echo $__env->make('components.page-header', [
    'title' => $pageTitle ?? 'Create New Asset',
    'subtitle' => 'Add a new asset to the inventory',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home'), 'icon' => 'home'],
        ['label' => 'Assets', 'url' => route('assets.index')],
        ['label' => 'Create']
    ],
    'actions' => '<a href="'.route('assets.index').'" class="btn btn-secondary">
        <i class="fa fa-arrow-left"></i> Back to List
    </a>'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  <div class="row">
    <div class="col-md-6">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Asset Information</h3>
        </div>
        <div class="box-body">
          <form method="POST" action="<?php echo e(url('assets')); ?>" id="asset-create-form">
            <?php echo e(csrf_field()); ?>

            <div class="form-group">
              <label for="serial_number">Serial Number</label>
              <input type="text" name="serial_number" id="serial_number" class="form-control" value="<?php echo e(old('serial_number')); ?>" autofocus>
            </div>
            <div class="form-group">
                <label for="model_id">Asset Model</label>
                <select name="model_id" id="model_id" class="form-control" required>
                    <option value="">-- Select Asset Model --</option>
            <?php
              // Prefer composer-provided `asset_models` (cached) but fall back to controller's $models
              $modelsList = isset($asset_models) ? $asset_models : (isset($models) ? $models : collect());
            ?>
            <?php $__currentLoopData = $modelsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($model->id); ?>"><?php echo e($model->manufacturer->name ?? ''); ?> - <?php echo e($model->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="form-group">
              <label for="division_id">Division</label>
              <select class="form-control division_id" name="division_id" id="division_id">
                <option value = ""></option>
                <?php $__currentLoopData = $divisions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $division): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($division->id); ?>"><?php echo e($division->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="supplier_id">Supplier</label>
              <select class="form-control supplier_id" name="supplier_id" id="supplier_id">
                <option value = ""></option>
                <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($supplier->id); ?>"><?php echo e($supplier->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="invoice_id">Invoice (Optional)</label>
              <select class="form-control invoice_id" name="invoice_id" id="invoice_id">
                <option value="">No Invoice</option>
                <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($invoice->id); ?>"><?php echo e($invoice->invoice_number); ?> - <?php echo e($invoice->invoiced_date); ?> - <?php echo e($invoice->supplier->name); ?> - R<?php echo e($invoice->total); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="purchase_date">Purchase Date</label>
              <input type="date" name="purchase_date" id="purchase_date" class="form-control" value="<?php echo e(old('purchase_date')); ?>">
            </div>
            <div class="form-group">
              <label for="warranty_months">Warranty Months</label>
              <input type="number" name="warranty_months" id="warranty_months" class="form-control" value="<?php echo e(old('warranty_months')); ?>">
            </div>
            <div class="form-group">
              <label for="warranty_type_id">Warranty Type</label>
              <select class="form-control warranty_type_id" name="warranty_type_id" id="warranty_type_id">
                <option value = ""></option>
                <?php $__currentLoopData = $warranty_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warranty_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($warranty_type->id); ?>"><?php echo e($warranty_type->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <!-- Computer-specific fields -->
            <div class="pc-laptop-fields" style="display: none;">
              <fieldset style="border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 4px;">
                <legend style="font-size: 14px; font-weight: bold; color: #337ab7;">Computer Specifications</legend>
                <div class="form-group">
                  <label for="ip_address">IP Address</label>
                  <input type="text" name="ip_address" id="ip_address" class="form-control" value="<?php echo e(old('ip_address')); ?>" placeholder="e.g., 192.168.1.100">
                </div>
                <div class="form-group">
                  <label for="mac_address">MAC Address</label>
                  <input type="text" name="mac_address" id="mac_address" class="form-control" value="<?php echo e(old('mac_address')); ?>" placeholder="e.g., 00:1B:44:11:3A:B7">
                </div>
              </fieldset>
            </div>
            <div class="form-group">
              <label for="asset_tag">Asset Tag <span class="text-red">*</span></label>
              <input type="text" name="asset_tag" id="asset_tag" class="form-control" value="<?php echo e(old('asset_tag')); ?>" required maxlength="10" placeholder="e.g., AST-001">
              <small class="text-muted">Maximum 10 characters, must be unique</small>
            </div>
            
            <div class="form-group">
              <label for="status_id">Status <span class="text-red">*</span></label>
              <select class="form-control status_id" name="status_id" id="status_id" required>
                <option value="">Select Status</option>
                <?php if(isset($statuses)): ?>
                  <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($status->id); ?>" <?php echo e(old('status_id') == $status->id ? 'selected' : ''); ?>><?php echo e($status->name); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                  <option value="1">In Stock</option>
                  <option value="2">In Use</option>
                  <option value="3">In Repair</option>
                  <option value="4">Disposed</option>
                <?php endif; ?>
              </select>
            </div>
            
            <div class="form-group">
              <label for="location">Deploy to a Location</label>
              <select class="form-control location" name="location" id="location">
                <option value = "">No</option>
                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($location->id); ?>"><?php echo e($location->location_name); ?> - <?php echo e($location->building); ?>, <?php echo e($location->office); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            
            <div class="form-group">
              <label for="notes">Notes</label>
              <textarea name="notes" id="notes" class="form-control" rows="3" maxlength="1000" placeholder="Additional notes about this asset..."><?php echo e(old('notes')); ?></textarea>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary btn-lg">
                <i class="fa fa-save"></i> <b>Add New Asset</b>
              </button>
              <a href="<?php echo e(route('assets.index')); ?>" class="btn btn-secondary btn-lg">
                <i class="fa fa-times"></i> Cancel
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Useful Links</h3>
        </div>
        <div class="box-body">
          <ul>
            <li><a href="http://h20564.www2.hp.com/hpsc/wc/public/home" target="_blank">HP Warranty Check</a></li>
            <li><a href="http://customercare.acer-euro.com/customerselfservice/CaseBooking.aspx?CID=ZA&LID=ENG&OP=1#_ga=1.185835882.214577358.1416317708" target="_blank">Acer Warranty Check</a></li>
          </ul>
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
  $('#asset-create-form').on('submit', function() {
    showLoading('Creating asset...');
  });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('footer'); ?>
  <script type="text/javascript">
    $(document).ready(function() {
      $(".model_id").select2();
      $(".division_id").select2();
      $(".supplier_id").select2();
      $(".location").select2();
      $(".warranty_type_id").select2();
      $(".invoice_id").select2();
      $(".status_id").select2();

      // Handle asset model change to show/hide conditional fields
      $('#model_id').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var assetType = selectedOption.data('asset-type');
        var assetTypeInfo = $('#asset-type-info');
        
        // Hide all conditional fields first
        $('.pc-laptop-fields').hide();
        
        if (assetType) {
          // Show asset type information
          assetTypeInfo.text('Asset Type: ' + assetType).show();
          
          // Show relevant fields based on asset type
          if (assetType.toLowerCase().includes('pc') || assetType.toLowerCase().includes('laptop')) {
            $('.pc-laptop-fields').show();
          }
        } else {
          // Hide asset type info if no selection
          assetTypeInfo.hide();
        }
      });

      // Trigger change event on page load if there's a selected value (for form validation errors)
      if ($('#model_id').val()) {
        $('#model_id').trigger('change');
      }
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



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/assets/create.blade.php ENDPATH**/ ?>