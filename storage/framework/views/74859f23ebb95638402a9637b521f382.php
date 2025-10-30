

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
              <label for="asset_tag">Kode Assets <span class="text-red">*</span></label>
              <input type="text" name="asset_tag" id="asset_tag" class="form-control" value="<?php echo e(old('asset_tag')); ?>" required maxlength="50" placeholder="e.g., AST-001">
            </div>

            <div class="form-group">
              <label for="asset_type_id">Kategori (Tipe Asset) <span class="text-red">*</span></label>
              <select name="asset_type_id" id="asset_type_id" class="form-control asset_type_id" required>
                <option value="">-- Pilih Kategori (Tipe) --</option>
                <?php $__currentLoopData = $asset_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $atype): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($atype->id); ?>" <?php echo e(old('asset_type_id') == $atype->id ? 'selected' : ''); ?>><?php echo e($atype->type_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

            <div class="form-group">
              <label for="model_id">Model (optional)</label>
              <select name="model_id" id="model_id" class="form-control model_id">
                <option value="">-- Pilih Model (optional) --</option>
                <?php $__currentLoopData = $asset_models; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($model->id); ?>" data-asset-type="<?php echo e($model->asset_type_id); ?>" <?php echo e(old('model_id') == $model->id ? 'selected' : ''); ?>><?php echo e($model->manufacturer->name ?? ''); ?> - <?php echo e($model->asset_model); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

            <div class="form-group">
              <label for="location_id">Lokasi <span class="text-red">*</span></label>
              <select class="form-control location_id" name="location_id" id="location_id" required>
                <option value="">-- Pilih Lokasi --</option>
                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($location->id); ?>" <?php echo e(old('location_id') == $location->id ? 'selected' : ''); ?>><?php echo e($location->location_name); ?> - <?php echo e($location->building); ?>, <?php echo e($location->office); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

            <div class="form-group">
              <label for="assigned_to">User / PIC <span class="text-red">*</span></label>
              <select name="assigned_to" id="assigned_to" class="form-control assigned_to" required>
                <option value="">-- Pilih User / PIC --</option>
                <?php $activeUsers = \App\User::where('is_active', 1)->orderBy('name')->get(); ?>
                <?php $__currentLoopData = $activeUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($u->id); ?>" <?php echo e(old('assigned_to') == $u->id ? 'selected' : ''); ?>><?php echo e($u->name); ?> (<?php echo e($u->email); ?>)</option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

            <div class="form-group">
              <label for="purchase_date">Tanggal Beli <span class="text-red">*</span></label>
              <input type="date" name="purchase_date" id="purchase_date" class="form-control" value="<?php echo e(old('purchase_date')); ?>" required>
            </div>

            <div class="form-group">
              <label for="supplier_id">Suplier <span class="text-red">*</span></label>
              <select class="form-control supplier_id" name="supplier_id" id="supplier_id" required>
                <option value="">-- Pilih Supplier --</option>
                <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($supplier->id); ?>" <?php echo e(old('supplier_id') == $supplier->id ? 'selected' : ''); ?>><?php echo e($supplier->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

            <div class="form-group">
              <label for="purchase_order_id">Purchase Order (Optional)</label>
              <select class="form-control purchase_order_id" name="purchase_order_id" id="purchase_order_id">
                <option value="">-- No Purchase Order --</option>
        <?php $__currentLoopData = $purchaseOrders ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($po->id); ?>" <?php echo e(old('purchase_order_id') == $po->id ? 'selected' : ''); ?>>
            <?php echo e($po->po_number); ?> - <?php echo e($po->order_date ? \Carbon\Carbon::parse($po->order_date)->format('Y-m-d') : ''); ?> - <?php echo e($po->supplier ? $po->supplier->name : ''); ?>

          </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

            <div class="form-group">
              <label for="warranty_type_id">Jenis Garansi <span class="text-red">*</span></label>
              <select class="form-control warranty_type_id" name="warranty_type_id" id="warranty_type_id" required>
                <option value="">-- Pilih Jenis Garansi --</option>
                <?php $__currentLoopData = $warranty_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warranty_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($warranty_type->id); ?>" <?php echo e(old('warranty_type_id') == $warranty_type->id ? 'selected' : ''); ?>><?php echo e($warranty_type->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

            <div class="form-group">
              <label for="notes">Spesifikasi <span class="text-red">*</span></label>
              <textarea name="notes" id="notes" class="form-control" rows="3" required><?php echo e(old('notes')); ?></textarea>
            </div>

            <div class="form-group">
              <label for="ip_address">IP Address</label>
              <input type="text" name="ip_address" id="ip_address" class="form-control" value="<?php echo e(old('ip_address')); ?>" placeholder="e.g., 192.168.1.100">
            </div>

            <div class="form-group">
              <label for="mac_address">MAC Address</label>
              <input type="text" name="mac_address" id="mac_address" class="form-control" value="<?php echo e(old('mac_address')); ?>" placeholder="e.g., 00:1B:44:11:3A:B7">
            </div>

            <div class="form-group">
              <label for="serial_number">S/N</label>
              <input type="text" name="serial_number" id="serial_number" class="form-control" value="<?php echo e(old('serial_number')); ?>">
              <small id="serial-feedback" class="text-muted" style="display:none"></small>
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
            
            
            <input type="hidden" name="status_id" value="<?php echo e(old('status_id', 1)); ?>">
            <input type="hidden" name="warranty_months" value="<?php echo e(old('warranty_months', 0)); ?>">

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
  <script>
    // Serial number uniqueness check (AJAX) for create form
    $(function(){
      $('#serial_number').on('blur', function(){
        var serial = $(this).val().trim();
        if (!serial) {
          $('#serial-feedback').hide();
          return;
        }
        $.getJSON('<?php echo e(route("api.assets.checkSerial")); ?>', { serial: serial })
          .done(function(resp){
            if (resp && resp.success) {
              if (resp.exists) {
                $('#serial-feedback').show().removeClass('text-muted text-success').addClass('text-danger').text('Serial number already exists in the system.');
              } else {
                $('#serial-feedback').show().removeClass('text-danger text-muted').addClass('text-success').text('Serial number available.');
              }
            }
          }).fail(function(){
            $('#serial-feedback').hide();
          });
      });
    });
  </script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer'); ?>
  <script type="text/javascript">
  $(document).ready(function() {
  $(".model_id").select2();
  $(".division_id").select2();
  $(".supplier_id").select2();
  $(".location").select2();
  $(".location_id").select2();
  $(".assigned_to").select2();
  $(".asset_type_id").select2();
  $(".warranty_type_id").select2();
  $(".invoice_id").select2();
  $(".status_id").select2();

      // Handle asset model change to show/hide conditional fields
      // When asset type changes, show/hide PC-specific fields and filter model list
      $('#asset_type_id').on('change', function() {
        var selectedText = $(this).find('option:selected').text();
        var selectedId = $(this).val();
        // Hide PC/Laptop fields by default
        $('.pc-laptop-fields').hide();

        if (selectedText && (selectedText.toLowerCase().includes('pc') || selectedText.toLowerCase().includes('laptop') || selectedText.toLowerCase().includes('computer'))) {
          $('.pc-laptop-fields').show();
        }

        // Filter model select options by data-asset-type
        $('#model_id option').each(function() {
          var mt = $(this).data('asset-type') ? String($(this).data('asset-type')) : '';
          if (!selectedId || mt === '' || mt === selectedId) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
        // Reset model selection if current option hidden
        if ($('#model_id option:selected').is(':hidden')) {
          $('#model_id').val('').trigger('change');
        }
      });

      // Trigger change event on page load if there's a selected value (for form validation errors)
      if ($('#model_id').val()) {
        $('#model_id').trigger('change');
      }
    });
  </script>
  <script>
    // Serial number uniqueness check (AJAX)
    $(function(){
      $('#serial_number').on('blur', function(){
        var serial = $(this).val().trim();
        if (!serial) {
          $('#serial-feedback').hide();
          return;
        }
        // Call API endpoint to check uniqueness
        $.getJSON('<?php echo e(route("api.assets.checkSerial")); ?>', { serial: serial })
          .done(function(resp){
            if (resp && resp.success) {
              if (resp.exists) {
                $('#serial-feedback').show().removeClass('text-muted text-success').addClass('text-danger').text('Serial number already exists in the system.');
              } else {
                $('#serial-feedback').show().removeClass('text-danger text-muted').addClass('text-success').text('Serial number available.');
              }
            }
          }).fail(function(){
            // silently fail (keep UX responsive)
            $('#serial-feedback').hide();
          });
      });
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



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\assets\create.blade.php ENDPATH**/ ?>