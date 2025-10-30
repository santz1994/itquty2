

<?php $__env->startSection('main-content'); ?>



<?php echo $__env->make('components.page-header', [
    'title' => 'Suppliers',
    'subtitle' => 'Manage vendors and suppliers',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home'), 'icon' => 'home'],
        ['label' => 'Suppliers']
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
            <i class="fa fa-truck"></i> All Suppliers 
            <span class="badge bg-blue count-badge"><?php echo e(count($suppliers)); ?></span>
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
                <th style="width: 60px;"><i class="fa fa-hashtag"></i> ID</th>
                <th><i class="fa fa-building"></i> Supplier Name</th>
                <th style="width: 150px;"><i class="fa fa-calendar"></i> Created Date</th>
                <th style="width: 100px;"><i class="fa fa-cogs"></i> Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td class="text-center"><?php echo e($supplier->id); ?></td>
                  <td><strong><?php echo e($supplier->name); ?></strong></td>
                  <td><?php echo e($supplier->created_at ? $supplier->created_at->format('M d, Y') : '-'); ?></td>
                  <td>
                    <a href="<?php echo e(route('suppliers.edit', $supplier->id)); ?>" class="btn btn-sm btn-primary" title="Edit Supplier">
                      <i class="fa fa-pencil"></i>
                    </a>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                  <td colspan="4" class="text-center empty-state">
                    <i class="fa fa-info-circle"></i> No suppliers found. Create one using the form on the right.
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
          <h3 class="box-title"><i class="fa fa-plus-circle"></i> Create New Supplier</h3>
        </div>
        <div class="box-body">
          <form method="POST" action="<?php echo e(url('suppliers')); ?>" id="create-supplier-form">
            <?php echo csrf_field(); ?>

            <fieldset>
              <legend>
                <span class="form-section-icon"><i class="fa fa-info-circle"></i></span>
                Supplier Details
              </legend>

              
              <div class="form-group <?php echo e($errors->has('name') ? 'has-error' : ''); ?>">
                <label for="name">
                  Supplier Name <span class="text-danger">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       class="form-control" 
                       value="<?php echo e(old('name')); ?>"
                       placeholder="e.g., Dell Technologies, HP Inc."
                       required>
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

            
            <div class="form-group" style="margin-top: 20px; padding-top: 15px; border-top: 2px solid #e3e3e3;">
              <button type="submit" class="btn btn-primary btn-block btn-submit">
                <i class="fa fa-plus-circle"></i> Add New Supplier
              </button>
            </div>
          </form>
        </div>
      </div>

      
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-lightbulb-o"></i> Supplier Guidelines</h3>
        </div>
        <div class="box-body info-box-custom">
          <h4><i class="fa fa-info-circle"></i> Best Practices</h4>
          <ul>
            <li><i class="fa fa-check text-success"></i> Use official company names</li>
            <li><i class="fa fa-check text-success"></i> Avoid abbreviations unless standard</li>
            <li><i class="fa fa-check text-success"></i> Check for duplicates before adding</li>
            <li><i class="fa fa-check text-success"></i> Keep naming consistent</li>
          </ul>

          <h4 style="margin-top: 15px;"><i class="fa fa-list"></i> Common Suppliers</h4>
          <ul>
            <li><i class="fa fa-building text-info"></i> Dell Technologies</li>
            <li><i class="fa fa-building text-info"></i> HP Inc.</li>
            <li><i class="fa fa-building text-info"></i> Lenovo</li>
            <li><i class="fa fa-building text-info"></i> Microsoft</li>
            <li><i class="fa fa-building text-info"></i> Cisco Systems</li>
          </ul>
        </div>
      </div>

      
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-bar-chart"></i> Quick Stats</h3>
        </div>
        <div class="box-body">
          <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="fa fa-truck"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Suppliers</span>
              <span class="info-box-number"><?php echo e(count($suppliers)); ?></span>
            </div>
          </div>
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
    order: [[1, "asc"]], // Sort by Supplier Name
    columnDefs: [{
      orderable: false,
      targets: 3 // Actions column
    }],
    dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>' +
         '<"row"<"col-sm-12"<"table-responsive"tr>>>' +
         '<"row"<"col-sm-5"i><"col-sm-7"p>>',
    language: {
      lengthMenu: "Show _MENU_ suppliers per page",
      info: "Showing _START_ to _END_ of _TOTAL_ suppliers",
      infoEmpty: "No suppliers available",
      infoFiltered: "(filtered from _MAX_ total suppliers)",
      search: "Search suppliers:",
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
  $('#create-supplier-form').on('submit', function(e) {
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



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/suppliers/index.blade.php ENDPATH**/ ?>