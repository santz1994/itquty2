

<?php $__env->startSection('main-content'); ?>



<?php echo $__env->make('components.page-header', [
    'title' => 'Edit Location',
    'subtitle' => 'Update location information',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home'), 'icon' => 'home'],
        ['label' => 'Locations', 'url' => route('locations.index')],
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

  
  <?php if($location->created_at): ?>
    <div class="alert alert-info metadata-alert">
      <strong><i class="fa fa-info-circle"></i> Location Info:</strong>
      Created on <?php echo e($location->created_at->format('M d, Y \a\t h:i A')); ?>

      <?php if($location->updated_at && $location->updated_at != $location->created_at): ?>
        | Last updated on <?php echo e($location->updated_at->format('M d, Y \a\t h:i A')); ?>

      <?php endif; ?>
    </div>
  <?php endif; ?>

  <div class="row">
    <div class="col-md-8">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-edit"></i> Edit Location Details</h3>
        </div>
        <div class="box-body">
          <form method="POST" action="<?php echo e(route('locations.update', $location->id)); ?>" id="edit-location-form">
            <?php echo method_field('PATCH'); ?>
            <?php echo csrf_field(); ?>

            <fieldset>
              <legend>
                <span class="form-section-icon"><i class="fa fa-map-marker"></i></span>
                Location Information
              </legend>

              
              <div class="form-group <?php echo e($errors->has('building') ? 'has-error' : ''); ?>">
                <label for="building">
                  Building <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-building"></i></span>
                  <input type="text" 
                         id="building" 
                         name="building" 
                         class="form-control" 
                         value="<?php echo e(old('building', $location->building)); ?>"
                         placeholder="e.g., Tower A, Main Building"
                         required>
                </div>
                <small class="help-text">
                  <i class="fa fa-info-circle"></i> Enter the building name or identifier
                </small>
                <?php $__errorArgs = ['building'];
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

              
              <div class="form-group <?php echo e($errors->has('office') ? 'has-error' : ''); ?>">
                <label for="office">
                  Office <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-door-open"></i></span>
                  <input type="text" 
                         id="office" 
                         name="office" 
                         class="form-control" 
                         value="<?php echo e(old('office', $location->office)); ?>"
                         placeholder="e.g., Floor 3, Room 301"
                         required>
                </div>
                <small class="help-text">
                  <i class="fa fa-info-circle"></i> Specify the floor or room number
                </small>
                <?php $__errorArgs = ['office'];
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

              
              <div class="form-group <?php echo e($errors->has('location_name') ? 'has-error' : ''); ?>">
                <label for="location_name">
                  Location Name <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-map-marker-alt"></i></span>
                  <input type="text" 
                         id="location_name" 
                         name="location_name" 
                         class="form-control" 
                         value="<?php echo e(old('location_name', $location->location_name)); ?>"
                         placeholder="e.g., IT Department, HR Office"
                         required>
                </div>
                <small class="help-text">
                  <i class="fa fa-info-circle"></i> Full descriptive name for this location
                </small>
                <?php $__errorArgs = ['location_name'];
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
                <i class="fa fa-save"></i> Update Location
              </button>
              <a href="<?php echo e(route('locations.index')); ?>" class="btn btn-default btn-lg">
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
            <li><i class="fa fa-warning text-warning"></i> <strong>Impact:</strong> Changing this location may affect assets and tickets assigned to it</li>
            <li><i class="fa fa-users text-info"></i> Assets and tickets will remain linked to this location</li>
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
          <a href="<?php echo e(route('locations.index')); ?>" class="btn btn-default btn-block">
            <i class="fa fa-list"></i> Back to All Locations
          </a>
          <a href="<?php echo e(route('assets.index', ['location_id' => $location->id])); ?>" class="btn btn-primary btn-block">
            <i class="fa fa-desktop"></i> View Assets at This Location
          </a>
        </div>
      </div>

      
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-lightbulb-o"></i> Best Practices</h3>
        </div>
        <div class="box-body info-box-custom">
          <ul>
            <li><i class="fa fa-check text-success"></i> Keep naming consistent</li>
            <li><i class="fa fa-check text-success"></i> Include building and floor</li>
            <li><i class="fa fa-check text-success"></i> Make names searchable</li>
            <li><i class="fa fa-check text-success"></i> Avoid special characters</li>
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
  $('#edit-location-form').on('submit', function(e) {
    var building = $('#building').val().trim();
    var office = $('#office').val().trim();
    var locationName = $('#location_name').val().trim();

    if (building === '' || office === '' || locationName === '') {
      e.preventDefault();
      alert('All fields are required!');
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



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/locations/edit.blade.php ENDPATH**/ ?>