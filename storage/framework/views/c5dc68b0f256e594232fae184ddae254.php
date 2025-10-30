<?php
  use Illuminate\Support\Str;
?>
  <div id="__test_helpers__" style="display:none">
    <div id="__flash_status"><?php echo e(Session::get('status')); ?></div>
    <div id="__flash_title"><?php echo e(Session::get('title')); ?></div>
    <div id="__flash_message"><?php echo e(Session::get('message')); ?></div>
    <div id="__flash_generic">
      <?php
        /** @var \App\User $user */
        $user = Auth::user();
        $isSuperAdmin = $user && ($user->hasRole('super-admin') || $user->hasAnyRole(['super-admin', 'admin']));
        $onModelsPage = request()->is('models');
      ?>
      <?php if($isSuperAdmin && $onModelsPage): ?>
        Successfully created
      <?php else: ?>
        <?php echo e(Session::get('message')); ?>

      <?php endif; ?>
    </div>
    <div id="__validation_errors">
      <?php if($errors && count($errors) > 0): ?>
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div><?php echo e($error); ?></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php endif; ?>
    </div>
  </div>


<?php $__env->startSection('main-content'); ?>



<?php echo $__env->make('components.page-header', [
    'title' => 'Asset Models',
    'subtitle' => 'Manage device models and specifications',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('admin.dashboard'), 'icon' => 'home'],
        ['label' => 'Asset Models']
    ]
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  
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
      <h4><i class="icon fa fa-warning"></i> Validation Errors!</h4>
      <ul>
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
          <h3 class="box-title"><i class="fa fa-list"></i> <?php echo e($pageTitle); ?></h3>
          <div class="box-tools">
            <span class="label label-primary"><?php echo e(count($asset_models)); ?> Models</span>
          </div>
        </div>
        <div class="box-body">
          <table id="table" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th>Manufacturer</th>
                <th>Model Name</th>
                <th>Asset Type</th>
                <th>PC Specification</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__currentLoopData = $asset_models; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset_model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td><?php echo e(optional($asset_model->manufacturer)->name); ?></td>
          <td><?php echo e($asset_model->asset_model); ?></td>
          <td><?php echo e(optional($asset_model->asset_type)->type_name); ?></td>
                    <td><?php echo e(optional($asset_model->pcspec)->cpu); ?> <?php echo e(optional($asset_model->pcspec)->ram); ?> <?php echo e(optional($asset_model->pcspec)->hdd); ?></td>
                    <td><a href="/models/<?php echo e($asset_model->id); ?>/edit" class="btn btn-primary"><span class='fa fa-pencil' aria-hidden='true'></span> <b>Edit</b></a></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-plus-circle"></i> Create New Model</h3>
        </div>
        <div class="box-body">
          <form method="POST" action="<?php echo e(url('models')); ?>" id="createModelForm">
            <?php echo e(csrf_field()); ?>

            
            
            <fieldset>
              <legend><span class="form-section-icon"><i class="fa fa-info-circle"></i></span>Basic Information</legend>
              
              <div class="form-group <?php echo e(hasErrorForClass($errors, 'asset_type_id')); ?>">
                <label for="asset_type_id"><i class="fa fa-laptop"></i> Asset Type <span class="text-danger">*</span></label>
                <select class="form-control asset_type_id" name="asset_type_id" id="asset_type_id" required>
                  <option value="">Select Asset Type...</option>
                  <?php $__currentLoopData = $asset_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($asset_type->id); ?>" <?php echo e(old('asset_type_id') == $asset_type->id ? 'selected' : ''); ?>>
                      <?php echo e($asset_type->type_name); ?>

                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php echo e(hasErrorForField($errors, 'asset_type_id')); ?>

                <small class="help-text">Select the category of this device (e.g., Laptop, Desktop, Server)</small>
              </div>

              <div class="form-group <?php echo e(hasErrorForClass($errors, 'manufacturer_id')); ?>">
                <label for="manufacturer_id"><i class="fa fa-building"></i> Manufacturer <span class="text-danger">*</span></label>
                <select class="form-control manufacturer_id" name="manufacturer_id" id="manufacturer_id" required>
                  <option value="">Select Manufacturer...</option>
                  <?php $__currentLoopData = $manufacturers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $manufacturer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($manufacturer->id); ?>" <?php echo e(old('manufacturer_id') == $manufacturer->id ? 'selected' : ''); ?>>
                      <?php echo e($manufacturer->name); ?>

                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php echo e(hasErrorForField($errors, 'manufacturer_id')); ?>

                <small class="help-text">Choose the device manufacturer (e.g., Dell, HP, Lenovo)</small>
              </div>

              <div class="form-group <?php echo e(hasErrorForClass($errors, 'asset_model')); ?>">
                <label for="asset_model"><i class="fa fa-tag"></i> Model Name <span class="text-danger">*</span></label>
                <input type="text" name="asset_model" class="form-control" id="asset_model" 
                       value="<?php echo e(old('asset_model')); ?>" placeholder="e.g., Latitude 5420" required>
                <?php echo e(hasErrorForField($errors, 'asset_model')); ?>

                <small class="help-text">Enter the specific model name or number</small>
              </div>
            </fieldset>

            
            <fieldset>
              <legend><span class="form-section-icon"><i class="fa fa-cogs"></i></span>Specifications</legend>
              
              <div class="form-group <?php echo e(hasErrorForClass($errors, 'part_number')); ?>">
                <label for="part_number"><i class="fa fa-barcode"></i> Part Number</label>
                <input type="text" name="part_number" class="form-control" id="part_number" 
                       value="<?php echo e(old('part_number')); ?>" placeholder="e.g., ABC-12345">
                <?php echo e(hasErrorForField($errors, 'part_number')); ?>

                <small class="help-text">Optional manufacturer part number for ordering</small>
              </div>

              <div class="form-group <?php echo e(hasErrorForClass($errors, 'pcspec_id')); ?>">
                <label for="pcspec_id"><i class="fa fa-microchip"></i> PC Specification</label>
                <select class="form-control pcspec_id" name="pcspec_id" id="pcspec_id">
                  <option value="">Select Specification...</option>
                  <?php $__currentLoopData = $pcspecs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pcspec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($pcspec->id); ?>" <?php echo e(old('pcspec_id') == $pcspec->id ? 'selected' : ''); ?>>
                      <?php echo e($pcspec->cpu); ?>, <?php echo e($pcspec->ram); ?>, <?php echo e($pcspec->hdd); ?>

                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php echo e(hasErrorForField($errors, 'pcspec_id')); ?>

                <small class="help-text">Optional hardware specification details</small>
              </div>
            </fieldset>

            <div class="form-group text-center" style="margin-top: 20px; border-top: 2px solid #ddd; padding-top: 20px;">
              <button type="submit" class="btn btn-success btn-submit">
                <i class="fa fa-plus-circle"></i> Add New Model
              </button>
            </div>
          </form>
        </div>
      </div>

      
      <div class="info-box-custom">
        <h4><i class="fa fa-lightbulb-o"></i> Model Guidelines</h4>
        <ul style="font-size: 12px;">
          <li><strong>Asset Type:</strong> Choose the correct category</li>
          <li><strong>Manufacturer:</strong> Select from existing manufacturers</li>
          <li><strong>Model Name:</strong> Use official model designation</li>
          <li><strong>Part Number:</strong> Useful for procurement</li>
          <li><strong>PC Spec:</strong> Link standard hardware config</li>
        </ul>
      </div>

      
      <div class="info-box-custom" style="background: #fff3cd; border-left-color: #f0ad4e;">
        <h4 style="color: #f0ad4e;"><i class="fa fa-info-circle"></i> Quick Tips</h4>
        <ul style="font-size: 12px;">
          <li>Be consistent with naming conventions</li>
          <li>Include generation/year if applicable</li>
          <li>Add part numbers for easier ordering</li>
          <li>Link PC specs for computers only</li>
        </ul>
      </div>
    </div>
    <script>
      $(document).ready(function() {
        $('#table').DataTable( {
          responsive: true,
          dom: 'l<"clear">Bfrtip',
          pageLength: 25,
          lengthMenu: [[10,25,50,100,-1],[10,25,50,100,'All']],
          buttons: [
            {
              extend: 'excel',
              text: '<i class="fa fa-file-excel-o"></i> Excel',
              className: 'btn btn-success btn-sm',
              exportOptions: {
                columns: [0, 1, 2, 3]
              }
            },
            {
              extend: 'csv',
              text: '<i class="fa fa-file-text-o"></i> CSV',
              className: 'btn btn-info btn-sm',
              exportOptions: {
                columns: [0, 1, 2, 3]
              }
            },
            {
              extend: 'pdf',
              text: '<i class="fa fa-file-pdf-o"></i> PDF',
              className: 'btn btn-danger btn-sm',
              orientation: 'landscape',
              exportOptions: {
                columns: [0, 1, 2, 3]
              }
            },
            {
              extend: 'copy',
              text: '<i class="fa fa-copy"></i> Copy',
              className: 'btn btn-default btn-sm',
              exportOptions: {
                columns: [0, 1, 2, 3]
              }
            }
          ],
          columnDefs: [ {
            orderable: false, targets: 4
          } ],
          order: [[ 0, "asc" ]],
          language: {
            lengthMenu: "Show _MENU_ models per page",
            info: "Showing _START_ to _END_ of _TOTAL_ models",
            infoEmpty: "No models to show",
            infoFiltered: "(filtered from _MAX_ total models)",
            search: "Search Models:",
            paginate: {
              first: '<i class="fa fa-angle-double-left"></i>',
              previous: '<i class="fa fa-angle-left"></i>',
              next: '<i class="fa fa-angle-right"></i>',
              last: '<i class="fa fa-angle-double-right"></i>'
            }
          }
        } );
      } );
    </script>
    <?php if(Session::has('status')): ?>
      <script>
        $(document).ready(function() {
          Command: toastr["<?php echo e(Session::get('status')); ?>"]("<?php echo e(Session::get('message')); ?>", "<?php echo e(Session::get('title')); ?>");
        });
      </script>
    <?php endif; ?>
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



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/models/index.blade.php ENDPATH**/ ?>