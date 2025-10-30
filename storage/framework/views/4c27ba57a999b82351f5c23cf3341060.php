

<?php $__env->startSection('main-content'); ?>




<?php echo $__env->make('components.page-header', [
    'title' => $pageTitle ?? 'Asset Types',
    'subtitle' => 'Manage asset type categories',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home'), 'icon' => 'home'],
        ['label' => 'Asset Types']
    ]
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="container-fluid">

    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fa fa-check-circle"></i> <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fa fa-exclamation-triangle"></i> <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

  <div class="row">
    <div class="col-md-9">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-tags"></i> Asset Types</h3>
          <span class="count-badge"><?php echo e(count($asset_types)); ?></span>
        </div>
        <div class="box-body">
          <table id="table" class="table table-enhanced table-striped table-hover">
            <thead>
              <tr>
                <th>Asset Type Name</th>
                <th style="width: 130px;">Abbreviation</th>
                <th style="width: 130px;">Track Spare</th>
                <th style="width: 140px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $asset_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><strong><?php echo e($asset_type->type_name); ?></strong></td>
                    <td><span class="label label-info"><?php echo e($asset_type->abbreviation); ?></span></td>
                    <td>
                      <?php if($asset_type->spare == 1): ?>
                        <span class="label label-success"><i class="fa fa-check"></i> Yes</span>
                      <?php else: ?>
                        <span class="label label-default"><i class="fa fa-times"></i> No</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div class="btn-group">
                        <a href="/asset-types/<?php echo e($asset_type->id); ?>/edit" class="btn btn-xs btn-warning" title="Edit">
                          <i class='fa fa-edit'></i>
                        </a>
                        <form method="POST" action="<?php echo e(url('asset-types/' . $asset_type->id)); ?>" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this asset type?');">
                          <?php echo e(csrf_field()); ?>

                          <?php echo e(method_field('DELETE')); ?>

                          <button type="submit" class="btn btn-xs btn-danger" title="Delete">
                            <i class="fa fa-trash"></i>
                          </button>
                        </form>
                      </div>
                    </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                  <td colspan="4" class="text-center empty-state">
                    <i class="fa fa-tags fa-3x" style="opacity: 0.3; margin-bottom: 15px;"></i>
                    <p>No asset types found.</p>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    
    <div class="col-md-3">
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-plus-circle"></i> Create New Type</h3>
        </div>
        <div class="box-body">
          <form method="POST" action="<?php echo e(url('asset-types')); ?>" id="asset-type-form">
            <?php echo e(csrf_field()); ?>

            
            <fieldset>
              <legend><span class="form-section-icon"><i class="fa fa-tag"></i></span> Type Details</legend>
              
              <div class="form-group <?php echo e(hasErrorForClass($errors, 'type_name')); ?>">
                <label for="type_name">Asset Type Name <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-tag"></i></span>
                  <input type="text" name="type_name" id="type_name" class="form-control" value="<?php echo e(old('type_name')); ?>" placeholder="e.g., Computer, Monitor" required>
                </div>
                <small class="help-text">Enter the asset type category name</small>
                <?php echo e(hasErrorForField($errors, 'type_name')); ?>

              </div>
              
              <div class="form-group <?php echo e(hasErrorForClass($errors, 'abbreviation')); ?>">
                <label for="abbreviation">Abbreviation <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-font"></i></span>
                  <input type="text" name="abbreviation" id="abbreviation" class="form-control" value="<?php echo e(old('abbreviation')); ?>" placeholder="e.g., COMP, MON" required maxlength="10">
                </div>
                <small class="help-text">Short code (max 10 characters)</small>
                <?php echo e(hasErrorForField($errors, 'abbreviation')); ?>

              </div>
              
              <div class="form-group <?php echo e(hasErrorForClass($errors, 'spare')); ?>">
                <label for="spare">Track Spare Level?</label>
                <select class="form-control spare" name="spare" id="spare">
                  <option value="0">No</option>
                  <option value="1">Yes</option>
                </select>
                <small class="help-text">Enable inventory tracking for spare parts</small>
                <?php echo e(hasErrorForField($errors, 'spare')); ?>

              </div>
            </fieldset>

            <div class="form-group" style="margin-top: 20px;">
              <button type="submit" class="btn btn-success btn-block">
                <i class="fa fa-plus"></i> <b>Add Asset Type</b>
              </button>
            </div>
          </form>
        </div>
      </div>

      
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-lightbulb"></i> Guidelines</h3>
        </div>
        <div class="box-body">
          <ul style="margin-left: 20px;">
            <li><i class="fa fa-check text-success"></i> Use clear, descriptive names</li>
            <li><i class="fa fa-check text-success"></i> Keep abbreviations short (3-5 chars)</li>
            <li><i class="fa fa-check text-success"></i> Enable spare tracking for consumables</li>
            <li><i class="fa fa-check text-success"></i> Avoid duplicate type names</li>
          </ul>
          
          <p style="margin-top: 15px;"><strong>Common Examples:</strong></p>
          <ul style="margin-left: 20px; font-size: 12px;">
            <li>Computer (COMP)</li>
            <li>Monitor (MON)</li>
            <li>Printer (PRINT)</li>
            <li>Network Device (NET)</li>
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
      dom: 'Bfrtip',
      buttons: [
        {
          extend: 'excelHtml5',
          text: '<i class="fa fa-file-excel"></i> Excel',
          className: 'btn btn-success btn-sm',
          exportOptions: { columns: [0, 1, 2] }
        },
        {
          extend: 'csvHtml5',
          text: '<i class="fa fa-file-csv"></i> CSV',
          className: 'btn btn-info btn-sm',
          exportOptions: { columns: [0, 1, 2] }
        },
        {
          extend: 'pdfHtml5',
          text: '<i class="fa fa-file-pdf"></i> PDF',
          className: 'btn btn-danger btn-sm',
          exportOptions: { columns: [0, 1, 2] }
        },
        {
          extend: 'copy',
          text: '<i class="fa fa-copy"></i> Copy',
          className: 'btn btn-default btn-sm',
          exportOptions: { columns: [0, 1, 2] }
        }
      ],
      columnDefs: [{ orderable: false, targets: 3 }],
      order: [[0, "asc"]],
      language: {
        search: "_INPUT_",
        searchPlaceholder: "Search asset types...",
        lengthMenu: "Show _MENU_ types",
        info: "Showing _START_ to _END_ of _TOTAL_ types",
        paginate: {
          first: '<i class="fa fa-angle-double-left"></i>',
          last: '<i class="fa fa-angle-double-right"></i>',
          next: '<i class="fa fa-angle-right"></i>',
          previous: '<i class="fa fa-angle-left"></i>'
        }
      }
    });

    // Move export buttons to header
    table.buttons().container().appendTo($('.box-header .box-title').parent());

    // Form validation
    $('#asset-type-form').on('submit', function(e) {
      if ($('#type_name').val().trim() === '') {
        alert('Please enter the asset type name');
        $('#type_name').focus();
        return false;
      }
      if ($('#abbreviation').val().trim() === '') {
        alert('Please enter an abbreviation');
        $('#abbreviation').focus();
        return false;
      }
      return true;
    });

    // Auto-dismiss alerts
    setTimeout(function() {
      $('.alert').fadeOut('slow');
    }, 5000);
  });

  <?php if(Session::has('status')): ?>
    $(document).ready(function() {
      Command: toastr["<?php echo e(Session::get('status')); ?>"]("<?php echo e(Session::get('message')); ?>", "<?php echo e(Session::get('title')); ?>");
    });
  <?php endif; ?>
</script>
<?php $__env->stopPush(); ?>
  <?php endif; ?>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/asset-types/index.blade.php ENDPATH**/ ?>