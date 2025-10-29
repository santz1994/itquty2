

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
              <label for="asset_tag">Kode Assets <span class="text-red">*</span></label>
              <input type="text" name="asset_tag" id="asset_tag" class="form-control" value="<?php echo e(old('asset_tag', $asset->asset_tag)); ?>" required maxlength="50">
            </div>

            <div class="form-group">
              <label for="asset_type_id">Kategori (Tipe Asset) <span class="text-red">*</span></label>
              <select name="asset_type_id" id="asset_type_id" class="form-control asset_type_id" required>
                <option value="">-- Pilih Kategori (Tipe) --</option>
                <?php $__currentLoopData = $asset_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $atype): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($atype->id); ?>" <?php echo e((old('asset_type_id', $asset->model->asset_type_id ?? '') == $atype->id) ? 'selected' : ''); ?>><?php echo e($atype->type_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

            <div class="form-group">
              <label for="model_id">Model (optional)</label>
              <select name="model_id" id="model_id" class="form-control model_id">
                <option value="">-- Pilih Model (optional) --</option>
                <?php $__currentLoopData = $asset_models; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset_model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($asset_model->id); ?>" data-asset-type="<?php echo e($asset_model->asset_type_id); ?>" <?php echo e((old('model_id', $asset->model_id) == $asset_model->id) ? 'selected' : ''); ?>><?php echo e($asset_model->manufacturer->name ?? ''); ?> - <?php echo e($asset_model->asset_model); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

            <div class="form-group">
              <label for="location_id">Lokasi <span class="text-red">*</span></label>
              <select class="form-control location_id" name="location_id" id="location_id" required>
                <option value="">-- Pilih Lokasi --</option>
                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($location->id); ?>" <?php echo e((old('location_id', $asset->location_id) == $location->id) ? 'selected' : ''); ?>><?php echo e($location->location_name); ?> - <?php echo e($location->building); ?>, <?php echo e($location->office); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

            <div class="form-group">
              <label for="assigned_to">User / PIC <span class="text-red">*</span></label>
              <select name="assigned_to" id="assigned_to" class="form-control assigned_to" required>
                <option value="">-- Pilih User / PIC --</option>
                <?php $activeUsers = \App\User::where('is_active', 1)->orderBy('name')->get(); ?>
                <?php $__currentLoopData = $activeUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($u->id); ?>" <?php echo e((old('assigned_to', $asset->assigned_to) == $u->id) ? 'selected' : ''); ?>><?php echo e($u->name); ?> (<?php echo e($u->email); ?>)</option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

            <div class="form-group">
              <label for="purchase_date">Tanggal Beli <span class="text-red">*</span></label>
              <input type="date" name="purchase_date" class="form-control" id="purchase_date" value="<?php echo e(old('purchase_date', optional($asset->purchase_date)->format('Y-m-d'))); ?>" required>
            </div>

            <div class="form-group">
              <label for="warranty_type_id">Jenis Garansi <span class="text-red">*</span></label>
              <select class="form-control warranty_type_id" name="warranty_type_id" id="warranty_type_id" required>
                <option value="">-- Pilih Jenis Garansi --</option>
                <?php $__currentLoopData = $warranty_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warranty_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($warranty_type->id); ?>" <?php echo e((old('warranty_type_id', $asset->warranty_type_id) == $warranty_type->id) ? 'selected' : ''); ?>><?php echo e($warranty_type->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

            <div class="form-group">
              <label for="supplier_id">Suplier <span class="text-red">*</span></label>
              <select class="form-control supplier_id" name="supplier_id" id="supplier_id" required>
                <option value="">-- Pilih Supplier --</option>
                <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($supplier->id); ?>" <?php echo e((old('supplier_id', $asset->supplier_id) == $supplier->id) ? 'selected' : ''); ?>><?php echo e($supplier->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

            <div class="form-group">
              <label for="notes">Spesifikasi <span class="text-red">*</span></label>
              <textarea name="notes" id="notes" class="form-control" rows="3" required><?php echo e(old('notes', $asset->notes)); ?></textarea>
            </div>

            <div class="form-group">
              <label for="ip_address">IP Address</label>
              <input type="text" name="ip_address" class="form-control" id="ip_address" value="<?php echo e(old('ip_address', $asset->ip_address)); ?>" placeholder="e.g., 192.168.1.100">
            </div>

            <div class="form-group">
              <label for="mac_address">MAC Address</label>
              <input type="text" name="mac_address" class="form-control" id="mac_address" value="<?php echo e(old('mac_address', $asset->mac_address)); ?>" placeholder="e.g., 00:1B:44:11:3A:B7">
            </div>

            <div class="form-group">
              <label for="serial_number">S/N</label>
              <input type="text" name="serial_number" id="serial_number" class="form-control" value="<?php echo e(old('serial_number', $asset->serial_number)); ?>">
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
      $(".assigned_to").select2();
      $(".asset_type_id").select2();
  $(".asset_type_id").select2();
    });
  </script>
  <script type="text/javascript">
    $(document).ready(function() {
      // When asset type changes in edit form, filter model options and toggle PC fields
      $('#asset_type_id').on('change', function() {
        var selectedText = $(this).find('option:selected').text();
        var selectedId = $(this).val();
        $('.pc-laptop-fields').hide();
        if (selectedText && (selectedText.toLowerCase().includes('pc') || selectedText.toLowerCase().includes('laptop') || selectedText.toLowerCase().includes('computer'))) {
          $('.pc-laptop-fields').show();
        }
        $('#model_id option').each(function() {
          var mt = $(this).data('asset-type') ? String($(this).data('asset-type')) : '';
          if (!selectedId || mt === '' || mt === selectedId) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
        if ($('#model_id option:selected').is(':hidden')) {
          $('#model_id').val('').trigger('change');
        }
      });
      // Trigger change on load to apply filtering if an asset type is already selected
      if ($('#asset_type_id').val()) {
        $('#asset_type_id').trigger('change');
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



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/assets/edit.blade.php ENDPATH**/ ?>