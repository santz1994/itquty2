

<?php $__env->startSection('main-content'); ?>



<?php echo $__env->make('components.page-header', [
    'title' => 'Locations',
    'subtitle' => 'Manage office locations and buildings',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home'), 'icon' => 'home'],
        ['label' => 'Locations']
    ]
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="container-fluid">
  
  <?php if(session('success') || Session::get('status') == 'success'): ?>
    <div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <i class="icon fa fa-check"></i> <?php echo e(session('success') ?? Session::get('message') ?? 'Operation completed successfully!'); ?>

    </div>
  <?php endif; ?>

  <?php if(session('error') || Session::get('status') == 'error'): ?>
    <div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <i class="icon fa fa-ban"></i> <?php echo e(session('error') ?? Session::get('message') ?? 'An error occurred!'); ?>

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

  <div class="row">
    <div class="col-md-9">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">
            <i class="fa fa-map-marker"></i> All Locations 
            <span class="badge bg-blue count-badge"><?php echo e(count($locations)); ?></span>
          </h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
              <i class="fa fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="box-body">
          <table id="table" class="table table-striped table-bordered table-hover table-enhanced">
            <thead>
              <tr>
                <th><i class="fa fa-building"></i> Building</th>
                <th><i class="fa fa-door-open"></i> Office</th>
                <th><i class="fa fa-map-marker-alt"></i> Location Name</th>
                <th style="width: 100px;"><i class="fa fa-cogs"></i> Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($location->building ?? '-'); ?></td>
                  <td><?php echo e($location->office ?? '-'); ?></td>
                  <td><strong><?php echo e($location->location_name); ?></strong></td>
                  <td>
                    <a href="<?php echo e(route('locations.edit', $location->id)); ?>" class="btn btn-sm btn-primary" title="Edit Location">
                      <i class="fa fa-pencil"></i>
                    </a>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                  <td colspan="4" class="text-center empty-state">
                    <i class="fa fa-info-circle"></i> No locations found. Create one using the form on the right.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-plus-circle"></i> Create New Location</h3>
        </div>
        <div class="box-body">
          <form method="POST" action="<?php echo e(url('locations')); ?>" id="create-location-form">
            <?php echo csrf_field(); ?>

            <fieldset>
              <legend>
                <span class="form-section-icon"><i class="fa fa-info-circle"></i></span>
                Location Details
              </legend>

              
              <div class="form-group <?php echo e($errors->has('building') ? 'has-error' : ''); ?>">
                <label for="building">
                  Building <span class="text-danger">*</span>
                </label>
                <input type="text" 
                       id="building" 
                       name="building" 
                       class="form-control" 
                       value="<?php echo e(old('building')); ?>"
                       placeholder="e.g., Tower A, Main Building"
                       required>
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
                <input type="text" 
                       id="office" 
                       name="office" 
                       class="form-control" 
                       value="<?php echo e(old('office')); ?>"
                       placeholder="e.g., Floor 3, Room 301"
                       required>
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
                <input type="text" 
                       id="location_name" 
                       name="location_name" 
                       class="form-control" 
                       value="<?php echo e(old('location_name')); ?>"
                       placeholder="e.g., IT Department, HR Office"
                       required>
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

            
            <div class="form-group" style="margin-top: 20px; padding-top: 15px; border-top: 2px solid #e3e3e3;">
              <button type="submit" class="btn btn-primary btn-block btn-submit">
                <i class="fa fa-plus-circle"></i> Add New Location
              </button>
            </div>
          </form>
        </div>
      </div>

      
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-lightbulb-o"></i> Location Guidelines</h3>
        </div>
        <div class="box-body info-box-custom">
          <h4><i class="fa fa-info-circle"></i> Best Practices</h4>
          <ul>
            <li><i class="fa fa-check text-success"></i> Use consistent naming conventions</li>
            <li><i class="fa fa-check text-success"></i> Include building and floor info</li>
            <li><i class="fa fa-check text-success"></i> Make names searchable and clear</li>
            <li><i class="fa fa-check text-success"></i> Group related locations together</li>
          </ul>

          <h4 style="margin-top: 15px;"><i class="fa fa-example"></i> Examples</h4>
          <ul>
            <li><strong>Building:</strong> Tower A</li>
            <li><strong>Office:</strong> Floor 3</li>
            <li><strong>Name:</strong> IT Department Office</li>
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
  // Enhanced DataTable with export buttons
  var table = $('#table').DataTable({
    responsive: true,
    pageLength: 25,
    order: [[2, "asc"]], // Sort by Location Name
    columnDefs: [{
      orderable: false,
      targets: 3 // Actions column
    }],
    dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>' +
         '<"row"<"col-sm-12"<"table-responsive"tr>>>' +
         '<"row"<"col-sm-5"i><"col-sm-7"p>>',
    language: {
      lengthMenu: "Show _MENU_ locations per page",
      info: "Showing _START_ to _END_ of _TOTAL_ locations",
      infoEmpty: "No locations available",
      infoFiltered: "(filtered from _MAX_ total locations)",
      search: "Search locations:",
      paginate: {
        first: '<i class="fa fa-angle-double-left"></i>',
        previous: '<i class="fa fa-angle-left"></i>',
        next: '<i class="fa fa-angle-right"></i>',
        last: '<i class="fa fa-angle-double-right"></i>'
      }
    },
    buttons: [
      {
        extend: 'excel',
        text: '<i class="fa fa-file-excel-o"></i> Excel',
        className: 'btn btn-success btn-sm',
        exportOptions: {
          columns: [0, 1, 2] // Exclude Actions column
        }
      },
      {
        extend: 'csv',
        text: '<i class="fa fa-file-text-o"></i> CSV',
        className: 'btn btn-info btn-sm',
        exportOptions: {
          columns: [0, 1, 2]
        }
      },
      {
        extend: 'pdf',
        text: '<i class="fa fa-file-pdf-o"></i> PDF',
        className: 'btn btn-danger btn-sm',
        exportOptions: {
          columns: [0, 1, 2]
        }
      },
      {
        extend: 'copy',
        text: '<i class="fa fa-copy"></i> Copy',
        className: 'btn btn-default btn-sm',
        exportOptions: {
          columns: [0, 1, 2]
        }
      }
    ]
  });

  // Add export buttons to header
  table.buttons().container()
    .appendTo($('.box-tools', '.box-primary .box-header'));

  // Form validation
  $('#create-location-form').on('submit', function(e) {
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



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/locations/index.blade.php ENDPATH**/ ?>