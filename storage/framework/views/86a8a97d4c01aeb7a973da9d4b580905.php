

<?php $__env->startSection('main-content'); ?>



<?php echo $__env->make('components.page-header', [
    'title' => 'Edit Supplier',
    'subtitle' => 'Update supplier information',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home'), 'icon' => 'home'],
        ['label' => 'Suppliers', 'url' => route('suppliers.index')],
        ['label' => 'Edit']
    ]
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="container-fluid">
  
  <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <i class="icon fa fa-check"></i> <?php echo e(session('success')); ?>

    </div>
  <?php endif; ?>

  <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <i class="icon fa fa-ban"></i> <?php echo e(session('error')); ?>

    </div>
  <?php endif; ?>

  <?php if($errors->any()): ?>
    <div class="alert alert-warning alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <h4><i class="icon fa fa-warning"></i> Please correct the following errors:</h4>
      <ul style="margin-bottom: 0;">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
    </div>
  <?php endif; ?>

  
  <?php if($supplier->created_at): ?>
    <div class="alert alert-info metadata-alert">
      <strong><i class="fa fa-info-circle"></i> Supplier Info:</strong>
      Created on <?php echo e($supplier->created_at->format('M d, Y \a\t h:i A')); ?>

      <?php if($supplier->updated_at && $supplier->updated_at != $supplier->created_at): ?>
        | Last updated on <?php echo e($supplier->updated_at->format('M d, Y \a\t h:i A')); ?>

      <?php endif; ?>
    </div>
  <?php endif; ?>

  <div class="row">
    <div class="col-md-8">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-edit"></i> Edit Supplier Details</h3>
        </div>
        <div class="box-body">
          <form method="POST" action="<?php echo e(route('suppliers.update', $supplier->id)); ?>" id="edit-supplier-form">
            <?php echo method_field('PATCH'); ?>
            <?php echo csrf_field(); ?>

            <fieldset>
              <legend>
                <span class="form-section-icon"><i class="fa fa-truck"></i></span>
                Supplier Information
              </legend>

              
              <div class="form-group <?php echo e($errors->has('name') ? 'has-error' : ''); ?>">
                <label for="name">
                  Supplier Name <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-building"></i></span>
                  <input type="text" 
                         id="name" 
                         name="name" 
                         class="form-control" 
                         value="<?php echo e(old('name', $supplier->name)); ?>"
                         placeholder="e.g., Dell Technologies, HP Inc."
                         required>
                </div>
                <small class="help-text">
                  <i class="fa fa-info-circle"></i> Enter the full legal or trading name of the supplier
                </small>
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <span class="help-block"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>
            </fieldset>

            
            <div class="form-group" style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e3e3e3;">
              <button type="submit" class="btn btn-primary btn-lg btn-submit">
                <i class="fa fa-save"></i> Update Supplier
              </button>
              <a href="<?php echo e(route('suppliers.index')); ?>" class="btn btn-default btn-lg">
                <i class="fa fa-arrow-left"></i> Cancel
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>

    
    <div class="col-md-4">
      
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-exclamation-triangle"></i> Edit Tips</h3>
        </div>
        <div class="box-body info-box-custom">
          <ul>
            <li><i class="fa fa-warning text-warning"></i> <strong>Impact:</strong> Changing this supplier may affect purchase orders and assets</li>
            <li><i class="fa fa-box text-info"></i> Existing purchase orders and assets will remain linked</li>
            <li><i class="fa fa-check text-success"></i> All changes are logged for audit purposes</li>
            <li><i class="fa fa-history text-muted"></i> You can view the change history after saving</li>
          </ul>
        </div>
      </div>

      
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-bolt"></i> Quick Actions</h3>
        </div>
        <div class="box-body">
          <a href="<?php echo e(route('suppliers.index')); ?>" class="btn btn-default btn-block">
            <i class="fa fa-list"></i> Back to All Suppliers
          </a>
          <?php if(isset($supplier->id)): ?>
            <a href="<?php echo e(route('assets.index', ['supplier_id' => $supplier->id])); ?>" class="btn btn-primary btn-block">
              <i class="fa fa-desktop"></i> View Assets from This Supplier
            </a>
          <?php endif; ?>
        </div>
      </div>

      
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-lightbulb-o"></i> Best Practices</h3>
        </div>
        <div class="box-body info-box-custom">
          <ul>
            <li><i class="fa fa-check text-success"></i> Use official company names</li>
            <li><i class="fa fa-check text-success"></i> Avoid abbreviations</li>
            <li><i class="fa fa-check text-success"></i> Keep naming consistent</li>
            <li><i class="fa fa-check text-success"></i> Check for duplicates</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
  // Form validation
  $('#edit-supplier-form').on('submit', function(e) {
    var supplierName = $('#name').val().trim();

    if (supplierName === '') {
      e.preventDefault();
      alert('Supplier name is required!');
      return false;
    }

    if (supplierName.length < 2) {
      e.preventDefault();
      alert('Supplier name must be at least 2 characters long!');
      return false;
    }
  });

  // Auto-dismiss alerts after 5 seconds
  setTimeout(function() {
    $('.alert-dismissible').fadeOut('slow');
  }, 5000);
});
</script>
<?php $__env->stopPush(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/suppliers/edit.blade.php ENDPATH**/ ?>